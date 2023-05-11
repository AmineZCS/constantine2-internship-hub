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


class SupervisorController extends Controller
{

    // sign up a new supervisor (validate the request and create a new user record and a new supervisor record) and return the token
    public function signUp(Request $request){
        $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email|unique:users,email',
            'password'=> 'required|min:6',
            'company_id' => 'required|exists:companies,id'
        ]);
        $user = User::create([
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
            'role' => 'supervisor'
        ]);
        $supervisor = Supervisor::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'id' => $user->id,
            'company_id' => $request->company_id
        ]);
        return response()->json([
            'token' => $user->createToken($request->email)->plainTextToken,
            'role' => $user->role
        ]);
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
        $supervisor = Supervisor::where('id', $user->id)->first();
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
                $attendance->date = $date;
                $attendance->is_present = $is_present;
                $attendance->save();
            }
        }
        return response()->json(['message' => 'success']);
    }


}

