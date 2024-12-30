<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function registerview(){
        return view('auth.register');

    }
    public function loginview(){
        return view('auth.login');

    }
    public function errorview(){
        return view('auth.error');

    }
    public function userprofileview(){
        return view('teacherlayout.teacherprofile');

    }
    public function adminprofileview(){
        return view('adminlayout.adminprofile');

    }
    public function requestrole(){
        return view('requestrole');
    }
    public function querylist(){
        return view('adminlayout.querylist');
    }
    public function adminmanageuser(){
        return view('adminlayout.adminmanageuser');
    }
    public function adminviewuserdetail($id){
        return view('adminlayout.adminviewuserdetail');
    }
    public function categoryview(){
        return view('adminlayout.admincategory');
    }
    public function approverequest(){
        return view('adminlayout.approverequest');
    }
    public function Coursesapprovelist(){
        return view('adminlayout.admincourselisting');
    }
    public function addcourse()
    {
        return view('teacherlayout.addcourse');
    }
    public function Courselistteacher()
    {
        return view('teacherlayout.courselist');
    }
    public function manageadmission()
    {
        return view('teacherlayout.manageadmission');
    }
    public function adminmanageadmission()
    {
        return view('adminlayout.adminmanageadmission');
    }
    public function displaycourseuserview(){
        return view('userlayout.usercourselist');
    }
    public function displaycoursedetailview($courseId){
        return view('userlayout.usercoursedetailview');
    }

}
