<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseInquiry;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function adminDashboardview()
    {
        return view('adminlayout.admindashboard');
    }
    public function getteacherrequests()
    {
        $requests = User::where('teacher_request_status', 'pending')
            ->select([
                'id',
                'name',
                'email',
                'occupation',
                'company_name',
                'teacher_expertise'
            ])
            ->get();

        return response()->json(['requests' => $requests]);
    }

    public function submitTeacherRequest(Request $request)
    {

        $user = Auth::user();

        // Prevent multiple requests
        if (in_array($user->teacher_request_status, ['pending', 'approved'])) {
            return response()->json([
                'message' => 'You already have an active or approved teacher request'
            ], 400);
        }

        $validatedData = $request->validate([
            'occupation' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'teacher_expertise' => 'required|string|max:1000',
            'linkedin' => 'nullable|url',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'instagram' => 'nullable|url'
        ]);
        // Update user with teacher request details
        /** @var \App\Models\User $user */
        $user->update([
            'occupation' => $validatedData['occupation'],
            'company_name' => $validatedData['company_name'],
            'teacher_expertise' => $validatedData['teacher_expertise'],
            'linkedin' => $validatedData['linkedin'],
            'facebook' => $validatedData['facebook'],
            'twitter' => $validatedData['twitter'],
            'instagram' => $validatedData['instagram'],
            'teacher_request_status' => 'pending',
            'teacher_request_submitted_at' => now()
        ]);

        return response()->json([
            'message' => 'Teacher request submitted successfully',
            'status' => 'pending'
        ]);
    }

    public function approve($userId)
    {
        $user = User::findOrFail($userId);

        // Ensure only pending requests can be approved
        if ($user->teacher_request_status !== 'pending') {
            return response()->json([
                'message' => 'Invalid request status'
            ], 400);
        }

        $user->update([
            'role' => 'teacher',
            'teacher_request_status' => 'approved',
            'teacher_request_processed_at' => now()
        ]);

        return response()->json([
            'message' => 'Teacher request approved successfully'
        ]);
    }

    public function reject($userId)
    {
        $user = User::findOrFail($userId);
    
    // Ensure only pending requests can be rejected
    if ($user->teacher_request_status !== 'pending') {
        return response()->json([
            'message' => 'Invalid request status'
        ], 400);
    }
    
    $user->update([
        'teacher_request_status' => 'rejected',
        'teacher_request_processed_at' => now(),
        
        // Clear all teacher-specific details
        'occupation' => null,
        'company_name' => null,
        'teacher_expertise' => null,
        'linkedin' => null,
        'facebook' => null,
        'twitter' => null,
        'instagram' => null,
        
        // Optional: Reset submitted timestamp
        'teacher_request_submitted_at' => null
    ]);
    
    return response()->json([
        'message' => 'Teacher request rejected successfully'
    ]);
    }
    public function adminfetchcourse(Request $request)
    {
        $status = $request->query('status');
        $courses = Course::with(['category', 'user' => function($query) {
            $query->select('id', 'name', 'profile_picture'); // Ensure profile_image is included
        }])
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->get();
        

        return response()->json([
            'success' => true,
            'data' => $courses,
        ]);
    }
    /**
     * Update the course status.
     */
    public function updateStatus(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $course->status = $request->input('status');
        $course->save();

        return response()->json(['message' => 'Course status updated successfully', 'course' => $course], 200);
    }
    /**
     * Generate enrollment reports
     */
    public function generateEnrollmentReport(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date'
        ]);

        $query = Enrollment::with(['user', 'course']);

        if (isset($validated['start_date'])) {
            $query->whereDate('created_at', '>=', $validated['start_date']);
        }

        if (isset($validated['end_date'])) {
            $query->whereDate('created_at', '<=', $validated['end_date']);
        }

        $enrollments = $query->get();

        return response()->json([
            'total_enrollments' => $enrollments->count(),
            'total_revenue' => $enrollments->sum('paid_amount'),
            'enrollments' => $enrollments
        ]);
    }
    public function getDashboardStatistics()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'course_categories' => Category::withCount('courses')->get(),
            'recent_enrollments' => Enrollment::with(['user', 'course'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
        ]);
    }
    public function adminuserquery(Request $request)
    {
        $query = User::query();

    // Filter by role if provided and not empty
    if ($request->has('role') && $request->input('role') !== '') {
        $query->where('role', $request->input('role'));
    }

    $users = $query->get();

    return response()->json([
        'success' => true,
        'data' => $users
    ]);
    }

    /**
     * Delete a user
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function admindestroyuser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
    /**
     * Get the profile information for a user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserProfile($userId)
    {
        $user = User::with('courses.category')->findOrFail($userId);
    
        $stats = [
            'total_courses' => $user->courses->count(),
            'active_courses' => $user->courses->where('status', 'approved')->count(),
            'total_students' => $user->courses->sum('num_students') ?? 0,
            'avg_course_price' => number_format($user->courses->where('status', 'approved')->avg('price'), 2),
            'member_since' => optional($user->created_at)->format('M Y') ?? 'N/A',
            'last_active' => optional($user->last_active_at)->format('M d, Y') ?? 'Not Recently Active',
            
        ];
    
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'company_name' => $user->company_name,
                    'mobile_number' => $user->phone,
                    'profile_picture' => $user->profile_picture,
                    'bio' => $user->bio,
                    'profile_picture' => $user->profile_picture 
                    ? asset('storage/' . $user->profile_picture) 
                    : asset('storage/courses/default.jpg'),
                    'linkedin' => $user->linkedin,
                    'facebook' => $user->facebook,
                    'twitter' => $user->twitter,
                    'instagram' => $user->instagram,
                ],
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Get the courses for a user, optionally filtered by status.
     *
     * @param  int  $id
     * @param  string|null  $status
     * @return \Illuminate\Http\JsonResponse
     */

     public function getUserCourses($userId, $status = null)
     {
         $user = User::with(['courses' => function($query) use ($status) {
             if ($status && $status !== 'all') {
                 $query->where('status', $status);
             }
         }, 'courses.category'])->findOrFail($userId);
     
         $statusCounts = [
             'all' => $user->courses->count(),
             'pending' => $user->courses->where('status', 'pending')->count(),
             'approved' => $user->courses->where('status', 'approved')->count(),
             'rejected' => $user->courses->where('status', 'rejected')->count()
         ];
     
         $courses = $user->courses->map(function($course) {
             return [
                 'id' => $course->id,
                 'title' => $course->title,
                 'category' => $course->category->name,
                 'level' => $course->skill_level,
                 'duration' => $course->duration . ' hours',
                 'num_students' => $course->num_students ?? 0,
                 'price' => '$' . number_format($course->price, 2),
                 'status' => $course->status,
                 'created_at' => $course->created_at->diffForHumans()
             ];
         });
     
         return response()->json([
             'success' => true,
             'data' => [
                 'courses' => $courses,
                 'statusFilter' => $status ?? 'all',
                 'statusCounts' => $statusCounts
             ]
         ]);
     }
     public function getTeacherCourses()
{
    $courses = Course::with(['enrollments', 'admissions'])
        ->get();
        
    return response()->json([
        'courses' => $courses
    ]);
}

public function getEnrollments()
{
    $enrollments = Enrollment::with(['user', 'course'])
        ->latest()
        ->paginate(10);
        
    return response()->json([
        'enrollments' => $enrollments
    ]);
}

public function getAdmissions()
{
    try {
        $admissions = Admission::with(['user', 'course'])
            ->latest()
            ->paginate(10);
            
        return response()->json([
            'admissions' => $admissions
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while fetching admissions',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function getAdmissionDetails(Admission $admission)
{
    try {
        $admission->load(['course', 'user']);
        
        return response()->json([
            'admission' => $admission
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error fetching admission details',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function adminupdateAdmissionStatus(Request $request, Admission $admission)
{
    $request->validate([
        'status' => 'required|in:pending,approved,rejected'
    ]);

    try {
        // Check if admission is already approved or rejected
        if (($admission->status === 'approved' || $admission->status === 'rejected') && 
            $admission->status !== $request->status) {
            return response()->json([
                'message' => 'Cannot modify status of processed admissions',
                'status' => $admission->status
            ], 422);
        }

        DB::beginTransaction();

        $admission->status = $request->status;
        $admission->save();

        if ($request->status === 'approved') {
            // Create enrollment only if it doesn't exist
            $existingEnrollment = Enrollment::where('user_id', $admission->user_id)
                ->where('course_id', $admission->course_id)
                ->first();

            if (!$existingEnrollment) {
                Enrollment::create([
                    'course_id' => $admission->course_id,
                    'user_id' => $admission->user_id,
                    'status' => 'pending',
                    'enrollment_date' => now(),
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'Status updated successfully',
            'admission' => $admission
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Error updating admission status',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
