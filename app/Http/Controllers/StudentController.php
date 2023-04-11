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
    // apply for an internship
    public function applyForInternship (Request $request)
    {
        $user = $request->user();
        $student = Student::where('id', $user->id)->first();
        $internship = Internship::where('id', $request->internship_id)->first();
        // create an application record
        $application = $student->applications()->create([
            'internship_id' => $internship->id,
            'student_id' => $student->id
        ]);
        return response()->json($application);
    }
    // get all applied internships
    public function getAppliedInternships (Request $request)
    {
        $user = $request->user();
        $student = Student::where('id', $user->id)->first();
        $internships = $student->internships()->get();
        return response()->json($internships);
    }
    // get all applications for the logged in student
    public function getApplications (Request $request)
    {
        $user = $request->user();
        $student = Student::where('id', $user->id)->first();
        $applications = $student->applications()->get();
        return response()->json($applications);
    }

}
