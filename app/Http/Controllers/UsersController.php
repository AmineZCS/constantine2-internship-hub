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

    // update the logged in user's profile picture (save the image in the public folder(id.jpg) and update the image path in the database)
    public function updateProfilePicture (Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        $user = $request->user();
        $imageName = $user->id.'.'.$request->image->extension();
        $request->image->move(public_path('profile_images'), $imageName);
        // get the user role
        $role = $user->role;
        // update the image path in the database based on the user role
        switch ($role) {
            case 'admin':
                $admin = Admin::where('id', $user->id)->first();
                $admin->image = $imageName;
                $admin->save();
                break;
            case 'supervisor':
                $supervisor = Supervisor::where('id', $user->id)->first();
                $supervisor->image = $imageName;
                $supervisor->save();
                break;
            case 'student':
                $student = Student::where('id', $user->id)->first();
                $student->photo_path = $imageName;
                $student->save();
                break;
            default:
                return response()->json(['message' => 'No role found']);
                break;
        }
        return response()->json(['message' => 'Profile picture updated successfully']);
    }
    // get profile picture of the logged in user
    public function getProfilePicture(Request $request){
        $userid = $request->user()->id;
        // get the user role
        $role = $request->user()->role;
        // get the image path from the database based on the user role
        switch ($role) {
            case 'admin':
                $admin = Admin::where('id', $userid)->first();
                $imageName = $admin->image;
                break;
            case 'supervisor':
                $supervisor = Supervisor::where('id', $userid)->first();
                $imageName = $supervisor->image;
                break;
            case 'student':
                $student = Student::where('id', $userid)->first();
                $imageName = $student->photo_path;
                break;
            default:
                return response()->json(['message' => 'No role found']);
                break;
        }
        $path = storage_path('app/public/pictures/profile_pics/' . $imageName);
    
        if (!file_exists($path)) {
            abort(404);
        }
    
        return response()->file($path);
    }
}      