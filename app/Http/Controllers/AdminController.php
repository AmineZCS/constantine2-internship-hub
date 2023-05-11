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
use App\Models\InternshipDepartment;
use App\Models\FeedbackApplication;

class AdminController extends Controller
{
    // sign up a new admin (validate the request and create a new user record and a new admin record) and return the token
    public function signUp(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password'=> 'required|min:6',
            'department_id' => 'required|exists:departments,id'
        ]);
        $user = User::create([
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
            'role' => 'admin'
        ]);
        $admin = Admin::create([
            'id' => $user->id,
            'department_id' => $request->department_id
        ]);
        return response()->json([
            'token' => $user->createToken($request->email)->plainTextToken,
            'role' => $user->role
        ]);
    }
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
        $admin = Admin::where('id', $user->id)->first();
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
    // get all applications in the same department as the logged in admin with company,student and internship details (company name and id, internship position and id, student name and id)
    public function getDepartmentApplications (Request $request)
    {
        $user = $request->user();
        $admin = Admin::where('id', $user->id)->first();
        $department = Department::where('id', $admin->department_id)->first();
        $applications = Application::join('internships', 'internships.id', '=', 'applications.internship_id')

            ->join('internship_department', 'internship_department.internship_id', '=', 'internships.id')
            ->join('companies', 'companies.id', '=', 'internships.company_id')
            ->join('students', 'students.id', '=', 'applications.student_id')
            ->join('users', 'users.id', '=', 'students.id')
            ->where('internship_department.department_id', $department->id)
            ->select('applications.*', 'companies.name as company_name', 'companies.id as company_id', 'internships.position as internship_position', 'internships.id as internship_id', 'students.fname as student_fname','students.lname as student_lname','users.email as student_email')
            ->get();
        return response()->json($applications);
    }
    // approve application
    // check if the application is for in internship in the same department as the logged in admin then check if the application is pending and update the admin_status to approved else return error message for every case
    public function approveApplication (Request $request)
    {
        $user = $request->user();
        $admin = Admin::where('id', $user->id)->first();
        $department = Department::where('id', $admin->department_id)->first();
        $application = Application::where('id', $request->application_id)->first();
        $internship = Internship::where('id', $application->internship_id)->first();
        $internship_department = InternshipDepartment::where('internship_id', $internship->id)->where('department_id', $department->id)->first();
        if ($internship_department) {
            if ($application->admin_status == 'pending') {
                $application->admin_status = 'approved';
                $application->save();
                return response()->json($application);
            } else {
                return response()->json(['error' => 'Application is not waiting for approval'], 400);
            }

        } else {
            return response()->json(['error' => 'Application is not for an internship in your department'], 400);
        }
    }
    // reject application and add the feedback to feedback_application table
    public function rejectApplication (Request $request)
    {
        // validate inputs
        $this->validate($request, [
            'feedback_id' => 'required|integer',
            'application_id' => 'required|integer',
        ]);
        $user = $request->user();
        $admin = Admin::where('id', $user->id)->first();
        $department = Department::where('id', $admin->department_id)->first();
        $application = Application::where('id', $request->application_id)->first();
        $internship = Internship::where('id', $application->internship_id)->first();
        $internship_department = InternshipDepartment::where('internship_id', $internship->id)->where('department_id', $department->id)->first();
        if ($internship_department) {

            if ($application->admin_status == 'pending') {
                $application->admin_status = 'rejected';
                $feedback_application = new FeedbackApplication();
                $feedback_application->application_id = $application->id;
                $feedback_application->feedback_id = $request->feedback_id;
                $feedback_application->save();
                $application->save();
                return response()->json($application);
            } else {
                return response()->json(['error' => 'Application is not waiting for approval'], 400);
            }

        } else {
            return response()->json(['error' => 'Application is not for an internship in your department'], 400);
        }
    }
}
