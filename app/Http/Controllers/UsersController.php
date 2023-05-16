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
use Illuminate\Support\Facades\Response;

class UsersController extends Controller
{
   //get profile informations based on the logged in user
   public function getProfile(Request $request)
   {
       $role = $request->user()->role;
       switch ($role) {
           case 'admin':
               $admin = Admin::join('users', 'users.id', '=', 'admins.id')
                   ->where('admins.id', $request->user()->id)
                   ->select('admins.*', 'users.email','users.role')
                   ->first();
               return response()->json($admin);
               break;
           case 'supervisor':
               $supervisor = Supervisor::join('users', 'users.id', '=', 'supervisors.id')
                   ->where('supervisors.id', $request->user()->id)
                   ->select('supervisors.*', 'users.email','users.role')
                   ->first();
               return response()->json($supervisor);
               break;
           case 'student':
               $student = Student::join('users', 'users.id', '=', 'students.id')
                   ->where('students.id', $request->user()->id)
                   ->select('students.*', 'users.email','users.role')
                   ->first();
               return response()->json($student);
               break;
           default:
               return response()->json(['message' => 'No role found']);
               break;
       }
   }


   // get all departments in an array of objects
    public function getDepartments()
    {
         $departments = Department::all();
         return response()->json($departments);
    }

    // get all companies in an array of objects
    public function getCompanies()
    {
        $companies = Company::all();
        return response()->json($companies);
    }

    // download the student's CV to the front end
    public function downloadCV(Request $request)
    {
        $student = Student::where('id', $request->id)->first();
        $pathToFile = storage_path('app/public/cvs/profile_cvs/' . $student->cv_path);
        return Response::download($pathToFile);
    }
}