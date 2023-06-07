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
use App\Models\Evaluation;
use App\Models\Attendance;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
// import the supervisorpassword email
use App\Mail\SupervisorPassword;
use Illuminate\Support\Str;
class StudentController extends Controller
{
    // sign up a new student (validate the request and create a new user record and a new student record) and return the token
    public function signUp(Request $request){
        $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            // email domain should be @univ-constantine2.dz
            'email' => 'required|email|unique:users,email|regex:/^[a-zA-Z0-9_.+-]+@univ-constantine2.dz$/',
            'password'=> 'required|min:6',
            'department_id' => 'required|exists:departments,id'
        ]);
        
        $user = User::create([
            'role' => 'student',
            'email'=> $request->email,
            'password'=> Hash::make($request->password)
            
        ]);
        $student = Student::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'id' => $user->id,
            'department_id' => $request->department_id
        ]);
        $user = User::where('id', $user->id)->first();
        $user_info = Student::where('id', $user->id)->first();
        Mail::to($user->email)->send(new WelcomeEmail($user, $user_info));
        return response()->json([
            'token' => $user->createToken($request->email)->plainTextToken,
            'role' => $user->role,
            'message' => 'Welcome Email sent successfully'
        ],200);
    }

   


    // get all internships in the same department as the logged in student
    public function getStudentInterns(Request $request)
    {
        $user = $request->user();
        $student = Student::where('id', $user->id)->first();
        $department = Department::where('id', $student->department_id)->first();
        $internships = Internship::join('internship_department', 'internship_department.internship_id', '=', 'internships.id')
            ->join('supervisors', 'supervisors.id', '=', 'internships.supervisor_id')
            ->join('companies', 'companies.id', '=', 'internships.company_id')
            ->join('users', 'users.id', '=', 'supervisors.id')
            ->where('internship_department.department_id', $department->id)
            ->where('supervisors.status', 'accepted')
            ->whereNotExists(function ($query) use ($student) {
                $query->select(DB::raw(1))
                    ->from('applications')
                    ->whereRaw('applications.internship_id = internships.id')
                    ->whereRaw('applications.student_id = ?', [$student->id]);
            })
            ->select('internships.*', 'companies.name as company_name', 'companies.email as company_email', 'companies.phone_number as company_phone_number', 'companies.bio as company_bio', 'companies.address as address', 'supervisors.fname as supervisor_fname', 'supervisors.lname as supervisor_lname', 'users.email as supervisor_email', 'supervisors.phone_number as supervisor_phone_number', 'supervisors.bio as supervisor_bio', 'supervisors.location as supervisor.location')
            ->get();
        return response()->json($internships);
    }




    // apply for an internship only if it's in the same department as the logged in student
    public function applyForInternship (Request $request)
    {
        $user = $request->user();
        $student = Student::where('id', $user->id)->first();
        $department = Department::where('id', $student->department_id)->first();
        $internship = Internship::join('internship_department', 'internship_department.internship_id', '=', 'internships.id')
            ->where('internship_department.department_id', $department->id)
            ->where('internships.id', $request->internship_id)
            ->first();
        if ($internship) {
            // create an application record
            $application = $student->applications()->create([
                'internship_id' => $internship->internship_id,
                'student_id' => $student->id
            ]);
            // get the application record with company and internship details
            $application = Application::join('internships', 'internships.id', '=', 'applications.internship_id')
                ->join('companies', 'companies.id', '=', 'internships.company_id')
                ->where('applications.id', $application->id)
                ->select('applications.*', 'companies.name as company_name', 'internships.position as internship_position')
                ->first();
            // create a notification record for the supervisor
            $notification = new Notification();
            $notification->user_id = $internship->supervisor_id;
            $notification->title = 'New Application';
            $notification->message = 'You have a new application for the internship ' . $internship->position;
            $notification->save();
            return response()->json($application);
        }else {
            return response()->json(['error' => 'Internship is not for your department'], 400);
        }
    }



    // get all applied internships
    public function getAppliedInternships (Request $request)
    {
        $user = $request->user();
        $student = Student::where('id', $user->id)->first();
        $internships = $student->internships()->get();
        return response()->json($internships);
    }



    // get all applications with company and internship details for the logged in student
    public function getStudentApplications(Request $request)
    {
        $user = $request->user();
        $student = Student::where('id', $user->id)->first();
        $applications = Application::with('internship.company', 'internship.supervisor.user', 'feedbacks')
            ->where('student_id', $student->id)
            ->get();
    
        return response()->json($applications);
    
    }
    // delete an application
    public function deleteApplication (Request $request)
    {
        $user = $request->user();
        $student = Student::where('id', $user->id)->first();
        $application = Application::where('id', $request->application_id)->first();
        if ($application->student_id == $student->id) {
            $application->delete();
            return response()->json(['message' => 'Application deleted successfully']);
        }else {
            return response()->json(['error' => 'You are not authorized to delete this application'], 400);
        }
    }

    // get all feedbacks for the given application
    public function getApplicationFeedbacks (Request $request)
    {
        $user = $request->user();
        $student = Student::where('id', $user->id)->first();
        $application = Application::where('id', $request->application_id)->first();
        if ($application->student_id == $student->id) {
            $feedbacks = Feedback::join('feedback_application', 'feedback_application.feedback_id', '=', 'feedbacks.id')
                ->where('feedback_application.application_id', $application->id)
                ->orderBy('feedbacks.created_at', 'asc')
                ->select('feedbacks.message', 'feedbacks.feedback_type')
                ->get();
            return response()->json($feedbacks);
        }else {
            return response()->json(['error' => 'You are not authorized to view this feedback'], 400);
        }
    }
     // upload the student's CV to the storage/app/public/cvs/profile_cvs/user_id
     public function uploadCV(Request $request)
     {
         $request->validate([
             'cv' => 'required|mimes:pdf|max:2048'
         ]);
         $user = $request->user();
         $student = Student::where('id', $user->id)->first();
         $cvName = $user->id.'.'.$request->cv->extension();
         $request->cv->move(storage_path('app/public/cvs/profile_cvs/'), $cvName);
         $student->cv_path = $cvName;
         $student->save();
         return response()->json(['message' => 'CV uploaded successfully']);
     }
    //  get the student evaluation for the internship where he has been accepted
    public function getStudentEvaluation (Request $request)
    {
        $user = $request->user();
            $evaluation = Evaluation::where('student_id', $user->id)
            ->where('supervisor_id', $request->supervisor_id)
            ->first();
            return response()->json($evaluation);
    }

    public function getAcceptedApplications(Request $request)
{
    $user = $request->user();
    $student = Student::where('id', $user->id)->first();
    $applications = Application::with('internship.supervisor')
        ->where('student_id', $student->id)
        ->where('supervisor_status', 'approved')
        ->where('admin_status', 'approved')
        ->get();

    return response()->json($applications);
}
    // get all attendance records for the logged in student
    public function getStudentAttendance (Request $request)
    {
        $user = $request->user();
        $student = Student::where('id', $user->id)->first();
        $attendance = Attendance::where('student_id', $student->id)->get();
        return response()->json($attendance);
    }
    // create a new supervisor and a new company and a new internship and apply for it and generate a random password for the supervisor
    public function createInternship (Request $request)
    {
        $request->validate([
            'supervisor_fname' => 'required|string',
            'supervisor_lname' => 'required|string',
            'supervisor_email' => 'required|email|unique:users,email',
            'supervisor_phone_number' => 'required|string',
            'supervisor_bio' => 'required|string',
            'supervisor_location' => 'required|string',
            'company_name' => 'required|string',
            'company_email' => 'required|email|unique:companies,email',
            'company_phone_number' => 'required|string',
            'company_bio' => 'required|string',
            'company_address' => 'required|string',
            'internship_position' => 'required|string',
            'internship_description' => 'required|string',
            'internship_start_date' => 'required|date',
            'internship_end_date' => 'required|date'
        ]);
        $company = Company::create([
            'name' => $request->company_name,
            'email' => $request->company_email,
            'phone_number' => $request->company_phone_number,
            'bio' => $request->company_bio,
            'address' => $request->company_address
        ]);
        // create a user record for the supervisor and generate a random password
        $password = Str::random(8);
        $user = User::create([
            'email' => $request->supervisor_email,
            'password' => Hash::make($password),
            'role' => 'supervisor'
        ]);
        $supervisor = Supervisor::create([
            'id' => $user->id,
            'fname' => $request->supervisor_fname,
            'lname' => $request->supervisor_lname,
            'phone_number' => $request->supervisor_phone_number,
            'bio' => $request->supervisor_bio,
            'location' => $request->supervisor_location,
            'company_id' => $company->id
        ]);
        
        $internship = Internship::create([
            'position' => $request->internship_position,
            'description' => $request->internship_description,
            'start_date' => $request->internship_start_date,
            'end_date' => $request->internship_end_date,
            'supervisor_id' => $supervisor->id,
            'location' => $request->internship_location,
            'company_id' => $company->id,
            'status' => 'open']);
            $departments_ids = $request->departments;
            // loop through the array of departments ids  and create a new internship_department record
            foreach ($departments_ids as $department_id) {
                $internship->departments()->attach($department_id);
            }
        // apply for the internship
        
        $student = Student::where('id', $request->user()->id)->first();
        $application = Application::create([
            'student_id' => $student->id,
            'internship_id' => $internship->id,
        ]);
        // send an email to the supervisor with the generated password
        Mail::to($user->email)->send(new SupervisorPassword($supervisor, $password));
        return response()->json(['message' => 'Internship created successfully']);
}
}
