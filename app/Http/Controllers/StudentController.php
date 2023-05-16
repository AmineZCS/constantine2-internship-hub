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
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        return response()->json([
            'token' => $user->createToken($request->email)->plainTextToken,
            'role' => $user->role
        ]);
    }

   


    // get all internships in the same department as the logged in student
    public function getStudentInterns (Request $request)
    {
        $user = $request->user();
        $student = Student::where('id', $user->id)->first();
        $department = Department::where('id', $student->department_id)->first();
        $internships = Internship::join('internship_department', 'internship_department.internship_id', '=', 'internships.id')
            ->where('internship_department.department_id', $department->id)
            ->select('internships.*')
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
    public function getStudentApplications (Request $request)
    {
        $user = $request->user();
        $student = Student::where('id', $user->id)->first();
        $applications = Application::join('internships', 'internships.id', '=', 'applications.internship_id')
            ->join('companies', 'companies.id', '=', 'internships.company_id')
            ->where('applications.student_id', $student->id)
            ->select('applications.*', 'companies.name as company_name', 'internships.position as internship_position')
            ->get();
        return response()->json($applications);
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


    // update the logged in student's profile picture (save the image in the public folder(id.jpg) and update the image path in the database)
    public function updateProfilePicture (Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        $user = $request->user();
        $student = Student::where('id', $user->id)->first();
        $imageName = $student->id.'.'.$request->image->extension();
        $request->image->move(public_path('profile_images'), $imageName);
        $student->photo_path = $imageName;
        $student->save();
        return response()->json(['message' => 'Profile picture updated successfully']);
    }




}
