<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Notification;
use App\Models\Admin;
use App\Models\Department;
use App\Models\Company;
use App\Models\Internship;
use App\Models\Application;
use App\Models\Feedback;
use App\Models\InternshipDepartment;
use App\Models\FeedbackApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use App\Mail\StudentApplicationDeclinedEmail;

class AdminController extends Controller
{
    // sign up a new admin (validate the request and create a new user record and a new admin record) and return the token
    public function signUp(Request $request){
        $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email|unique:users,email',
            'password'=> 'required|min:6',
            'department_id' => 'required|exists:departments,id'
        ]);
        // check if the department has an admin
        $admin = Admin::where('department_id', $request->department_id)->first();
        if ($admin) {
            return response()->json([
                'message' => 'This department already has an admin'
            ], 400);
        }
        $user = User::create([
            'role' => 'admin',
            'email'=> $request->email,
            'password'=> Hash::make($request->password)
        ]);
        $admin = Admin::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'id' => $user->id,
            'department_id' => $request->department_id
        ]);
        $user = User::where('id', $user->id)->first();
        $user_info = Admin::where('id', $user->id)->first();
        Mail::to($user->email)->send(new WelcomeEmail($user, $user_info));
        return response()->json([
            'token' => $user->createToken($request->email)->plainTextToken,
            'role' => $user->role,
            'message' => 'Welcome Email sent successfully'
        ],200);
    }
    // get all students in the same department as the logged in admin and join user's email
    public function getStudents (Request $request)
    {
        $user = $request->user();
        $admin = Admin::where('id', $user->id)->first();
        $department = Department::where('id', $admin->department_id)->first();
        $students = Student::join('users', 'users.id', '=', 'students.id')
                ->where('students.department_id', $department->id)
                ->select('students.*', 'users.email')
                ->get();
        return response()->json($students);
    }

    // get all internships in the same department as the logged in admin (where supervisor status is accepted) (join company name)
    public function getDepartmentInterns (Request $request)
    {
        $user = $request->user();
        $admin = Admin::where('id', $user->id)->first();
        $department = Department::where('id', $admin->department_id)->first();
        $internships = Internship::join('internship_department', 'internship_department.internship_id', '=', 'internships.id')
            ->join('companies', 'companies.id', '=', 'internships.company_id')
            ->join('supervisors', 'supervisors.id', '=', 'internships.supervisor_id')
            ->where('internship_department.department_id', $department->id)
            ->where('supervisors.status', 'accepted')
            ->select('internships.*', 'companies.name as company_name', 'supervisors.fname as supervisor_fname', 'supervisors.lname as supervisor_lname')
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
        $feedbacks = Feedback::where('feedback_type', 'admin')->where('is_default', true)->orWhere('sender_id', $user->id)->get();
        return response()->json($feedbacks);
    }
    // get all applications in the same department as the logged in admin with company,student and internship details (company name and id, internship position and id, student name and id)
    public function getDepartmentApplications(Request $request)
{
    $user = $request->user();
    $admin = Admin::where('id', $user->id)->first();
    $department = Department::where('id', $admin->department_id)->first();

    if (!$department) {
        return response()->json(['message' => 'Department not found'], 404);
    }

    $application = Application::with('student');
    $applications = Application::join('internships', 'internships.id', '=', 'applications.internship_id')
        ->join('internship_department', 'internship_department.internship_id', '=', 'internships.id')
        ->join('companies', 'companies.id', '=', 'internships.company_id')
        ->join('supervisors', 'supervisors.id', '=', 'internships.supervisor_id')
        ->join('students', 'students.id', '=', 'applications.student_id')
        ->join('users as student_users', 'student_users.id', '=', 'students.id')
        ->join('users as supervisor_users', 'supervisor_users.id', '=', 'supervisors.id')
        ->where('internship_department.department_id', $department->id)
        ->selectRaw('
    applications.id as application_id,
    applications.supervisor_status,
    applications.admin_status,
    applications.created_at,
    applications.updated_at,
    internships.*,
    internships.id as internship_id,
    internships.status as internship_status,
    internships.location as internship_location,
    companies.*,
    companies.name as company_name,
    companies.address as company_address,
    companies.phone_number as company_phone_number,
    companies.email as company_email,
    supervisors.*,
    supervisors.fname as supervisor_fname,
    supervisors.lname as supervisor_lname,
    supervisors.bio as supervisor_bio,
    supervisors.location as supervisor_location,
    supervisors.phone_number as supervisor_phone_number,
    students.*,
    students.id as student_id,
    student_users.email as student_email,
    supervisor_users.email as supervisor_email
')
        ->get()
        ->map(function ($application) {
            return [
                'application' => [
                    'id' => $application->application_id,
                    'supervisor_status' => $application->supervisor_status,
                    'admin_status' => $application->admin_status,
                    'created_at' => $application->created_at,
                    'updated_at' => $application->updated_at,
                ],
                'internship' => [
                    'id' => $application->internship_id,
                    'position' => $application->position,
                    'description' => $application->description,
                    'start_date' => $application->start_date,
                    'end_date' => $application->end_date,
                    'company_id' => $application->company_id,
                    'status' => $application->internship_status,
                    'location' => $application->internship_location,
                    'supervisor_id' => $application->supervisor_id,
                ],
                'student' => [
                    'id' => $application->student_id,
                    'fname' => $application->fname,
                    'lname' => $application->lname,
                    'email' => $application->student_email,
                    'bio' => $application->bio,
                    'location' => $application->location,
                    'phone_number' => $application->phone_number,
                    // Add other student fields as needed
                ],
                'supervisor' => [
                    'id' => $application->supervisor_id,
                    'fname' => $application->supervisor_fname,
                    'lname' => $application->supervisor_lname,
                    'email' => $application->supervisor_email,
                    'bio' => $application->supervisor_bio,
                    'location' => $application->supervisor_location,
                    'phone_number' => $application->supervisor_phone_number,
                ],
                'company' => [
                    'id' => $application->company_id,
                    'name' => $application->company_name,
                    'email' => $application->company_email,
                    'location' => $application->company_address,
                    'phone_number' => $application->company_phone_number,
                    // Add other company fields as needed
                ],
            ];
        });
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
                $application->admin_status = 'approved';
                $application->save();
                // create a new notification for  the student
                $student = Student::where('id', $application->student_id)->first();
                $notification = new Notification;
                $notification->user_id = $student->id;
                $notification->title = 'Application Approved';
                $notification->message = 'Your application for the ' . $internship->position . ' internship has been approved by the administrator';
                $notification->save();
                // create a new notification for  the supervisor
                $supervisor = Supervisor::where('id', $internship->supervisor_id)->first();
                $notification = new Notification;
                $notification->user_id = $supervisor->id;
                $notification->title = 'New Application';
                $notification->message = 'A student has applied for the ' . $internship->position . ' internship';
                $notification->save();
                return response()->json($application);
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
        $feedback_message = Feedback::where('id', $request->feedback_id)->select('message')->first();
        $internship_department = InternshipDepartment::where('internship_id', $internship->id)->where('department_id', $department->id)->first();
        if ($internship_department) {
                $application->admin_status = 'rejected';
                $feedback_application = new FeedbackApplication();
                $feedback_application->application_id = $application->id;
                $feedback_application->feedback_id = $request->feedback_id;
                $feedback_application->save();
                $application->save();
                // create a new notification for  the student
                $student = Student::where('id', $application->student_id)->first();
                $notification = new Notification;
                $notification->user_id = $student->id;
                $notification->title = 'Application Rejected';
                $notification->message = 'Your application for the ' . $internship->position . ' internship has been rejected by the administrator';
                $notification->save();
                // send email to student with the feedback (use )
                // create a data array to pass to the email view (it has fname and lname of student , feedback message , internship position , admin name)
                $data = [
                    'fname' => $student->fname,
                    'lname' => $student->lname,
                    'feedback' => $feedback_message->message,
                    'position' => $internship->position,
                    'admin_name' => $admin->fname . ' ' . $admin->lname,
                ];
                $receiver = User::where('id', $student->id)->first();
                Mail::to($receiver->email)->send(new StudentApplicationDeclinedEmail($data));
                
               return response()->json($application);

        } else {
            return response()->json(['error' => 'Application is not for an internship in your department'], 400);
        }
    }
    // get all supervisors (id, fname, lname, email,+ join company)
    public function getSupervisors (Request $request)
    {
        $supervisors = Supervisor::join('companies', 'companies.id', '=', 'supervisors.company_id')
            ->join('users', 'users.id', '=', 'supervisors.id')
            ->select('supervisors.*','users.email as supervisor_email' , 'companies.name as company_name', 'companies.email as company_email','companies.phone_number as company_phone_number','companies.bio as company_bio','companies.address as address')
            ->get();
        return response()->json($supervisors);
    }

    // accept a supervisor account
    public function acceptSupervisor (Request $request)
    {
        // validate inputs
        $this->validate($request, [
            'supervisor_id' => 'required|integer',
        ]);
        $supervisor = Supervisor::where('id', $request->supervisor_id)->first();
        $supervisor->status = 'accepted';
        $supervisor->save();
        return response()->json([
            'message' => 'Supervisor account accepted',
        ], 200);
    }
    // reject a supervisor account
    public function rejectSupervisor (Request $request)
    {   
        $this->validate($request, [
            'supervisor_id' => 'required|integer',
        ]);
        $supervisor = Supervisor::where('id', $request->supervisor_id)->first();
        $supervisor->status = 'rejected';
        $supervisor->save();
        return response()->json([
            'message' => 'Supervisor account rejected',
        ], 200);
    }
}
