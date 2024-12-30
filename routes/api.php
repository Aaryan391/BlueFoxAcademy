<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CourseInquiryController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/courses/categories', [CourseController::class, 'getCategories']);
Route::get('/courses/available', [UserController::class, 'getAvailableCourses']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware(['auth:sanctum'])->group(function () {
    // Profile Routes
    Route::get('/user', [ProfileController::class, 'getProfile']); // Get user data
    Route::post('/user/update', [ProfileController::class, 'updateProfile']);

    // User can submit a teacher request
    Route::post('/teacher-requests', [AdminController::class, 'submitTeacherRequest']);
});
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Dashboard Statistics
    Route::get('/dashboard/statistics', [AdminController::class, 'getDashboardStatistics']);
    // Reports
    Route::post('/reports/enrollments', [AdminController::class, 'generateEnrollmentReport']);
    //category manage
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    // Get all pending teacher requests
    Route::get('/teacher-requests', [AdminController::class, 'getteacherrequests']);

    // Approve a teacher request
    Route::post('/teacher-requests/{userId}/approve', [AdminController::class, 'approve']);

    // Reject a teacher request
    Route::post('/teacher-requests/{userId}/reject', [AdminController::class, 'reject']);
    Route::get('/admin/courses', [AdminController::class, 'adminfetchcourse']);
    Route::put('/admin/courses/{id}/status', [AdminController::class, 'updateStatus']);
    // manage user
    Route::get('/admin/user/{userId}', [AdminController::class, 'getUserProfile']);
    Route::get('/admin/user/{userId}/courses/{status?}', [AdminController::class, 'getUserCourses']);
    Route::get('/adminqueryuser', [AdminController::class, 'adminuserquery']);
    Route::delete('/admin/user/{id}', [AdminController::class, 'admindestroyuser']);
    //inqueryroute
    Route::get('/course-inquiries', [CourseInquiryController::class, 'querylistview']);
    Route::patch('/course-inquiries/{inquiry}/status', [CourseInquiryController::class, 'updateStatusquery']);
    // Enrollment Management and admission
    Route::get('/admin/courses/fetch', [AdminController::class, 'getTeacherCourses']);
    // Enrollments
    Route::get('/admin/enrollments', [AdminController::class, 'getEnrollments']);
    Route::get('/admin/admissions', [AdminController::class, 'getAdmissions']);
    Route::get('/admin/admissions/{admission}', [AdminController::class, 'getAdmissionDetails']);
    Route::post('/admin/admissions/{admission}/status', [AdminController::class, 'adminupdateAdmissionStatus']);
});
Route::middleware(['auth:sanctum', 'role:teacher'])->group(function () {
    Route::get('/teacher/courses', [CourseController::class, 'courselist']);
    Route::get('/courses/{courseId}', [CourseController::class, 'show']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::delete('/courses/{courseId}', [CourseController::class, 'destroy']);
    // Dashboard
    Route::get('/teacher/dashboard', [TeacherController::class, 'getDashboard']);

    // Course Management
    Route::post('/teacher/update/courses/{courseId}', [CourseController::class, 'updateCourse']);

    // Enrollment Management and admission
    // Courses
    Route::get('/teacher/courses/fetch', [TeacherController::class, 'getTeacherCourses']);
    // Enrollments
    Route::get('/teacher/enrollments', [TeacherController::class, 'getEnrollments']);
    Route::post('/teacher/enrollments/{enrollment}/status', [TeacherController::class, 'updateEnrollmentStatus']);
    // Admissions
    Route::get('/teacher/admissions', [TeacherController::class, 'getAdmissions']);
    Route::get('/teacher/admissions/{admission}', [TeacherController::class, 'getAdmissionDetails']);
});
Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::post('/teacher-requests', [AdminController::class, 'submitTeacherRequest']);
    Route::post('/course-admission', [UserController::class, 'submit']);
});
Route::get('/courses', [UserController::class, 'index']); // Courses List for Users
Route::get('/course/{courseId}', [UserController::class, 'show']);
Route::post('/course-inquiry', [CourseInquiryController::class, 'store']);
Route::get('course/{courseId}', [UserController::class, 'getCourseDetails']);
