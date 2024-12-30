<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function courselist()
{
    // Get the courses that belong to the logged-in user, eager load category
    $courses = Course::with('category')
                     ->where('user_id', Auth::id()) // Filter by the logged-in user's ID
                     ->get();

    // Return the filtered courses with category data
    return response()->json([
        'courses' => $courses
    ]);
}

    public function store(Request $request)
    {
        // Ensure only teachers can add courses
        if (Auth::user()->role !== 'teacher') {
            return response()->json([
                'message' => 'Unauthorized. Only teachers can add courses.'
            ], 403);
        }

        // Validation rules
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'skill_level' => 'required|in:Beginner,Intermediate,Advanced',
            'language' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',

            // Modify validation for has_assessments
            'has_assessments' => 'required|boolean',

            'course_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'type' => 'required|in:onsite,remote',
            'schedule' => 'nullable|json'
        ]);

        // Check validation
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle file upload
        $imagePath = null;
        if ($request->hasFile('course_image')) {
            $imagePath = $request->file('course_image')->store('courses', 'public');
        }

        // Explicitly convert has_assessments to integer
        $hasAssessments = $request->boolean('has_assessments') ? 1 : 0;
        // Create the course
        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'skill_level' => $request->skill_level,
            'language' => $request->language,
            'num_students' => 0,

            // Use the converted integer value
            'has_assessments' => $hasAssessments,

            'category_id' => $request->category_id,
            'user_id' => Auth::id(),
            'status' => 'pending',
            'course_image' => $imagePath,
            'schedule' => $request->input('schedule'),
            'type' => $request->type
        ]);

        return response()->json([
            'message' => 'Course created successfully',
            'course' => $course
        ], 201);
    }
    /**
     * Update course details
     */

    public function updateCourse(Request $request, $courseId)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'duration' => 'sometimes|integer|min:1',
            'skill_level' => 'sometimes|in:Beginner,Intermediate,Advanced',
            'language' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'has_assessments' => 'sometimes|boolean',
            'course_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:4096',
            'schedule' => 'sometimes|json',
            'type' => 'sometimes|in:onsite,remote'
            
            
        ]);

        $course = Course::where('user_id', Auth::id())
            ->findOrFail($courseId);
            $validated['status'] = 'pending';

        // Handle file upload if a new image is provided
        if ($request->hasFile('course_image')) {
            $imagePath = $request->file('course_image')->store('courses', 'public');
            $validated['course_image'] = $imagePath;
        }

        // Convert schedule to JSON if provided
        if (isset($validated['schedule'])) {
            $validated['schedule'] =($validated['schedule']);
        }

        // Convert boolean to integer for has_assessments
        if (isset($validated['has_assessments'])) {
            $validated['has_assessments'] = $validated['has_assessments'] ? 1 : 0;
        }

        if (!$course->update($validated)) {
            return response()->json(['message' => 'Failed to update course'], 500);
        }
        
        return response()->json([
            'message' => 'Course updated successfully',
            'course' => $course
        ]);
    }
    public function show($courseId)
    {
        $course = Course::with('category')->findOrFail($courseId); // Eager load category
    return response()->json(['course' => $course]);
    }
    public function destroy($courseId)
    {
        $course = Course::where('user_id', Auth::id())
            ->findOrFail($courseId);

        // Delete associated image
        if ($course->course_image) {
            Storage::disk('public')->delete($course->course_image);
        }

        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully'
        ]);
    }
    /**
     * Fetch all categories for course creation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories()
{
    try {
        // Fetch categories without role restrictions
        $categories = Category::all();
        
        return response()->json([
            'categories' => $categories
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error fetching categories',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
