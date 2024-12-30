<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserController extends Controller
{
    // Fetch the list of available courses for the user
    public function index()
    {
        // Get the list of approved courses (you can add filters like 'user enrolled', etc.)
        $courses = Course::with('category')
            ->where('status', 'approved')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $courses
        ]);
    }

    // Fetch the details of a single course by ID
    public function show($id)
    {
        // Find the course by its ID or fail if not found
        $course = Course::findOrFail($id);

        // Return the course data as JSON
        return response()->json([
            'category' => $course->category->name,
            'status' => 'success',
            'data' => $course
        ]);
    }
    public function getCourseDetails($courseId)
    {
        $course = Course::with('category')->findOrFail($courseId);
        $isPurchased = false;
        
        if (Auth::check()) {
            $isPurchased = Enrollment::where('user_id', Auth::id())
                                   ->where('course_id', $courseId)
                                   ->whereIn('status', ['active', 'completed'])
                                   ->exists();
        }
        
        return response()->json([
            'data' => array_merge($course->toArray(), ['is_purchased' => $isPurchased])
        ]);
    }
    public function submit(Request $request)
    {
        try {
            // Validate the input data
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'mobile_number' => 'required|string|max:20',
                'type' => 'required|in:remote,onsite',
                'schedule' => 'required|json'
            ]);
    
            // Check if user already has an admission for this course
            $existingAdmission = Admission::where('user_id', Auth::id())
                ->where('course_id', $request->course_id)
                ->whereIn('status', ['pending', 'approved'])
                ->first();
    
            if ($existingAdmission) {
                return response()->json([
                    'message' => 'You already have an active admission request for this course'
                ], 422);
            }
    
            // Decode and validate the schedule JSON
            $scheduleData = json_decode($request->schedule, true);
            if (!isset($scheduleData['day']) || !isset($scheduleData['start_time']) || !isset($scheduleData['end_time'])) {
                return response()->json([
                    'message' => 'Invalid schedule format'
                ], 422);
            }
    
            // Fetch the selected course details
            $course = Course::findOrFail($request->course_id);
    
            // Verify that the selected type matches the course type
            if ($request->type !== $course->type) {
                return response()->json([
                    'message' => 'Invalid course type selected'
                ], 422);
            }
    
            // Verify that the selected schedule is one of the available course schedules
            $courseSchedules = json_decode($course->schedule, true) ?? [];
            $validSchedule = false;
            foreach ($courseSchedules as $schedule) {
                if ($schedule['day'] === $scheduleData['day'] &&
                    $schedule['start_time'] === $scheduleData['start_time'] &&
                    $schedule['end_time'] === $scheduleData['end_time']) {
                    $validSchedule = true;
                    break;
                }
            }
    
            if (!$validSchedule) {
                return response()->json([
                    'message' => 'Invalid schedule selected'
                ], 422);
            }
    
            // Use database transaction to ensure data consistency
            DB::beginTransaction();
            try {
                // Create admission record with selected schedule and type
                $admission = Admission::create([
                    'course_id' => $course->id,
                    'user_id' => Auth::id(),
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'mobile_number' => $request->mobile_number,
                    'type' => $request->type,
                    'schedule' => $request->schedule,
                    'status' => 'pending'
                ]);
    
                // Create enrollment record
                $enrollment = Enrollment::create([
                    'course_id' => $course->id,
                    'user_id' => Auth::id(),
                    'status' => 'pending',
                    'enrollment_date' => Carbon::now(),
                ]);
    
                DB::commit();
    
                return response()->json([
                    'message' => 'Admission request submitted successfully',
                    'data' => [
                        'admission' => $admission,
                        'enrollment' => $enrollment
                    ]
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error submitting admission request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
