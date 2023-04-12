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
use App\Models\Application;
use App\Models\Feedback;

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
    // create a new feedback
    public function createAdminFeedback (Request $request)
    {
        $user = $request->user();
        $feedback = new Feedback();
        $feedback->feedback_type = 'admin';
        $feedback->sender_id = $user->id;
        $feedback->message = $request->message;
        $feedback->is_default = $request->is_default;
        $feedback->save();
        return response()->json($feedback);
    }
    // get all admin feedbacks
    public function getAdminFeedbacks (Request $request)
    {
        $user = $request->user();
        $feedbacks = Feedback::where('feedback_type', 'admin')->where('sender_id', $user->id)->orWhere('is_default', true)->get();
        return response()->json($feedbacks);
    }
    // get all applications in the same department as the logged in admin with company and internship details (company name and id, internship position)

    public function getDepartmentApplications (Request $request)
    {
        $user = $request->user();
        $admin = Admin::where('id', $user->id)->first();
        $department = Department::where('id', $admin->department_id)->first();
        $applications = Application::join('internships', 'internships.id', '=', 'applications.internship_id')
            ->join('companies', 'companies.id', '=', 'internships.company_id')
            ->join('internship_department', 'internship_department.internship_id', '=', 'internships.id')
            ->where('internship_department.department_id', $department->id)
            ->select('applications.*', 'companies.name as company_name', 'internships.position as internship_position', 'companies.id as company_id')
            ->get();
        return response()->json($applications);
    }



}
