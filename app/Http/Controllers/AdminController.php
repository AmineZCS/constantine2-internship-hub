<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Admin;
use App\Models\Department;
use App\Models\Company;
use App\Models\Internship;

class AdminController extends Controller
{
    // get all students in the same department as the logged in admin and join user's email
    public function getStudents (Request $request)
    {
        $user = $request->user();
        $admin = Admin::where('user_id', $user->id)->first();
        $department = Department::where('id', $admin->department_id)->first();
        $students = Student::join('users', 'users.id', '=', 'students.id')
                ->where('students.department_id', $department->id)
                ->select('students.*', 'users.email')
                ->get();
        return response()->json($students);
    }

    // get all internships in the same department as the logged in admin
    public function getDepartmentInterns (Request $request)
    {
        $user = $request->user();
        $admin = Admin::where('user_id', $user->id)->first();
        $department = Department::where('id', $admin->department_id)->first();
        $internships = Internship::join('internship_department', 'internship_department.internship_id', '=', 'internships.id')
            ->where('internship_department.department_id', $department->id)
            ->select('internships.*')
            ->get();
        return response()->json($internships);
    }
    // get all admin default feedbacks
    public function getAdminDefaultFeedbacks (Request $request)
    {
        $feedbacks = Feedback::where('feedback_type', 'admin')->where('is_default', true)->get();
        return response()->json($feedbacks);
    }


}
