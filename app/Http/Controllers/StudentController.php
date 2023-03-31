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

class StudentController extends Controller
{


    //get profile infos based on the logged in student
    public function getStudentInfo($request)
    {
        $student = Student::join('users', 'users.id', '=', 'students.user_id')
            ->where('students.user_id', $request->user()->id)
            ->select('students.*', 'users.email')
            ->first();
        return response()->json($student);
    }
    
}
