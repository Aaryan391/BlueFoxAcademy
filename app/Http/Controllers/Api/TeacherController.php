<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function teacherdashboardview()
    {
        return view('teacherlayout.teacherdashboard');
    }

    /**
     * Get teacher's dashboard data with error handling
     */
    public function getDashboard()
    {
        try {
            // Verify authenticated user
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Fetch courses with error handling
            $courses = Course::where('user_id', Auth::id())
                ->with(['enrollments' => function ($query) {
                    // Retrieve all enrollments, not just active ones
                    $query->with('user:id,name')->orderBy('created_at', 'desc');
                }])
                ->get()
                ->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'status' => $course->status ?? 'pending',
                        'active_enrollments' => $course->enrollments->count()
                    ];
                });

            // Calculate statistics
            $totalCourses = $courses->count();
            $totalStudents = $courses->sum(function ($course) {
                return $course['active_enrollments'];
            });

            // Fetch recent enrollments from all courses of the logged-in teacher
            $recentEnrollments = Enrollment::whereIn('course_id', $courses->pluck('id'))
                ->with(['user:id,name', 'course:id,title'])
                ->latest()
                ->take(5)
                ->get();

            // Calculate performance metrics safely
            $performanceMetrics = [
                'average_students_per_course' => $totalCourses > 0
                    ? round($totalStudents / $totalCourses, 2)
                    : 0
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'courses' => $courses,
                    'total_courses' => $totalCourses,
                    'total_students' => $totalStudents,
                    'recent_enrollments' => $recentEnrollments,
                    'performance_metrics' => $performanceMetrics
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching dashboard data',
                'debug_message' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    public function getTeacherCourses()
    {
        $courses = Course::where('user_id', Auth::id())
            ->with(['enrollments', 'admissions'])
            ->get();

        return response()->json([
            'courses' => $courses
        ]);
    }

    public function getEnrollments()
    {
        $enrollments = Enrollment::with(['user', 'course'])
            ->whereHas('course', function ($query) {
                $query->where('user_id', Auth::id());
            })
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
                ->whereHas('course', function ($query) {
                    $query->where('user_id', Auth::id());
                })
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

    public function updateAdmissionStatus(Request $request, Admission $admission)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        try {
            // Check if admission is already approved or rejected
            if (($admission->status === 'approved' || $admission->status === 'rejected') &&
                $admission->status !== $request->status
            ) {
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
    public function updateEnrollmentStatus(Request $request, Enrollment $enrollment)
    {
        try {
            DB::beginTransaction();

            // Find the associated admission and lock the record
            $admission = Admission::where('user_id', $enrollment->user_id)
                ->where('course_id', $enrollment->course_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Strict check for admission status
            if ($admission->status !== 'approved') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment status cannot be changed - admission must be approved first',
                    'admission_status' => $admission->status
                ], 422);
            }

            // Validate the requested status
            $request->validate([
                'status' => 'required|in:pending,active,completed'
            ]);

            // Update enrollment status
            $enrollment->status = $request->status;
            if ($request->status === 'completed') {
                $enrollment->completion_date = now();
            }

            $enrollment->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'enrollment' => $enrollment
            ]);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Associated admission record not found'
            ], 404);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating enrollment status'
            ], 500);
        }
    }
    
}
