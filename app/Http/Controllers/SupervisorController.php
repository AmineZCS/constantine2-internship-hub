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
use App\Models\User;
use App\Models\Application;
use App\Models\FeedbackApplication;
use App\Models\Feedback;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Attendance;
class SupervisorController extends Controller
{

    // sign up a new company and a new supervisor (validate the request and create a new user record and a new supervisor record) and return the token
    public function signUp(Request $request){
        $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email|unique:users,email',
            'password'=> 'required|min:6',
            // phone number , bio and image are optional
            'user_phone_number' => 'nullable',
            'user_bio' => 'nullable',
            'user_image' => 'nullable',
            // company details
            'company_name' => 'required',
            'company_email' => 'required|email|unique:companies,email',
            'company_phone_number' => 'nullable',
            'company_address' => 'nullable',
            'company_bio' => 'nullable',
            'company_image' => 'nullable',
        ]);
        // create a company record with the company details from the request
        $company = Company::create([
            'name' => $request->company_name,
            'email' => $request->company_email,
            'phone_number' => $request->company_phone_number,
            'address' => $request->company_address,
            'bio' => $request->company_bio,
            'image' => $request->company_image
        ]);

        $user = User::create([
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
            'role' => 'supervisor'
        ]);
        $supervisor = Supervisor::create([
            'id' => $user->id,
            'fname' => $request->fname,
            'lname' => $request->lname,
            'id' => $user->id,
            'company_id' => $company->id,
            'phone_number' => $request->user_phone_number,
            'bio' => $request->user_bio,

        ]);
        $user = User::where('id', $user->id)->first();
        $user_info = Supervisor::where('id', $user->id)->first();
        Mail::to($user->email)->send(new WelcomeEmail($user, $user_info));
        return response()->json([
            'token' => $user->createToken($request->email)->plainTextToken,
            'role' => $user->role,
            'message' => 'Welcome Email sent successfully'
        ],200);
        
    }
    

    
    // create a new internship and assign it to the logged in supervisor and create a new internship_department record based on the array of departments_ids
    public function createInternship (Request $request)
    {
        $user = $request->user();
        $supervisor = Supervisor::where('id', $user->id)->first();
        $internship = Internship::create([
            'position' => $request->position,
            'description' => $request->description,
            'location' => $request->location,
            'supervisor_id' => $supervisor->id,
            'company_id' => $supervisor->company_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'open'
        ]);
        $departments_ids = $request->departments;
        // loop through the array of departments ids  and create a new internship_department record
        foreach ($departments_ids as $department_id) {
            $internship->departments()->attach($department_id);
        }

        return response()->json($internship);
    }
    // get all internships created by the logged in supervisor
    public function getSupervisorInterns (Request $request)
    {
        $user = $request->user();
        $supervisor = Supervisor::where('id', $user->id)->first();
        $internships = Internship::where('supervisor_id', $supervisor->id)->get();
        return response()->json($internships);
    }
    // create supervisor feedback
    public function createSupervisorFeedback (Request $request)
    {
        $user = $request->user();
        $feedback = new Feedback();
        $feedback->feedback_type = 'supervisor';
        $feedback->sender_id = $user->id;
        $feedback->message = $request->message;
        $feedback->is_default = $request->is_default;
        $feedback->save();
        return response()->json($feedback);
    }
    // get all suppervisor feedbacks (default and custom)
    public function getSupervisorFeedbacks (Request $request)
    {
        $user = $request->user();
        $supervisor = Supervisor::where('id', $user->id)->first();
        $feedbacks = Feedback::where('feedback_type', 'supervisor')->where('sender_id', $supervisor->id)->orWhere('is_default', true)->get();
        return response()->json($feedbacks);
    }
    // attendance management
    // mark students attendance for a specific date (accept an array of objects (student_id + is_present))
    public function markAttendance (Request $request)
    {
        $user = $request->user();
        $internshipid = Internship::where('supervisor_id', $user->id)->first()->id;
        $date = $request->date;
        $arrayOfAttendance = $request->attendance;
        foreach ($arrayOfAttendance as $student) {
            $student_id = $student['student_id'];
            $is_present = $student['is_present'];
            $attendance = Attendance::where('student_id', $student_id)->where('date', $date)->first();
            if ($attendance) {
                $attendance->is_present = $is_present;
                $attendance->save();
            } else {
                $attendance = new Attendance();
                $attendance->student_id = $student_id;
                $attendance->internship_id = $internshipid;
                $attendance->date = $date;
                $attendance->is_present = $is_present;
                $attendance->save();
            }
        }
        return response()->json(['message' => 'success']);
    }
    // get all students attendance for a specific date
    public function getAttendance (Request $request)
    {
        $user = $request->user();
        $internshipid = Internship::where('supervisor_id', $user->id)->first()->id;
        $date = $request->date;
        $attendance = Attendance::where('internship_id', $internshipid)
        ->where('date', $date)
        ->with('student')
        ->get();
        return response()->json($attendance);
    }
    // get all applications for the logged in supervisor (with the student details including his email)
    public function getApplications (Request $request)
    {
        $user = $request->user();
        $supervisor = Supervisor::where('id', $user->id)->first();
        $internships = Internship::where('supervisor_id', $supervisor->id)->get();
        $applications = [];
        foreach ($internships as $internship) {
            $internship_applications = $internship->applications()->with('student.user:id,email', 'student.department:id,abbreviation')->get();
            foreach ($internship_applications as $application) {
                $student_email = $application->student->user->email;
                $department_abr = $application->student->department->abbreviation;
                $application_data = $application->toArray();
                $application_data['student']['email'] = $student_email;
                $application_data['student']['department'] = $department_abr;
                unset($application_data['student']['user']);
                unset($application_data['student']['department_abr']);
                $applications[] = $application_data;
            }
        }
        return response()->json($applications);
    }
    // accept internship application (change the supervisor status of the application)
    public function acceptApplication (Request $request)
    {
        $application_id = $request->application_id;
        $application = Application::where('id', $application_id)->first();
        $application->supervisor_status = "approved";
        $application->save();
        return response()->json(['message' => 'success']);
    }
    // reject internship application (change the supervisor status of the application)
    public function rejectApplication(Request $request)
{
    // validate inputs
    $this->validate($request, [
        'feedback_id' => 'required|integer',
        'application_id' => 'required|integer',
    ]);

    $user = $request->user();
    $supervisor = Supervisor::where('id', $user->id)->first();
    $application = Application::where('id', $request->application_id)->first();
    $internship = Internship::where('id', $application->internship_id)->first();
    $internship_supervisor = Internship::where('id', $internship->id)->where('supervisor_id', $supervisor->id)->first();

    if ($internship_supervisor) {
        if ($application->supervisor_status == 'pending') {
            $application->supervisor_status = 'rejected';
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
        return response()->json(['error' => 'Application is not for an internship you supervise'], 400);
    }
}
}