<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\ViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/register', [ViewController::class,'registerview']);
Route::get('/login', [ViewController::class,'loginview'])->name('login');
Route::get('/error', [ViewController::class,'errorview'])->name('error');
Route::get('/adminprofile', [ViewController::class,'adminprofileview']);
Route::get('/admindashboard', [AdminController::class,'adminDashboardview']);
Route::get('/teacherprofile', [ViewController::class,'userprofileview']);
Route::get('/add-course', [ViewController::class,'addcourse']);
Route::get('/category', [ViewController::class,'categoryview']);
Route::get('/requestrole', [ViewController::class,'requestrole']);
Route::get('/approverequest', [ViewController::class,'approverequest']);
Route::get('/Coursesapprovelist', [ViewController::class,'Coursesapprovelist']);
Route::get('/courselist', [ViewController::class,'Courselistteacher']);
Route::get('/teacherdashboard', [TeacherController::class,'teacherdashboardview']);
Route::get('/adminmanageuser', [ViewController::class,'adminmanageuser']);
Route::get('/admin/user/{id}', [ViewController::class,'adminviewuserdetail']);
Route::get('/usercourselist', [ViewController::class,'displaycourseuserview']);
Route::get('/course/{courseId}', [ViewController::class,'displaycoursedetailview']);
Route::get('/querylist', [ViewController::class,'querylist']);
Route::get('/manageadmission', [ViewController::class,'manageadmission']);
Route::get('/adminmanageadmission', [ViewController::class,'adminmanageadmission']);