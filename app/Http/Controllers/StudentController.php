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

}
