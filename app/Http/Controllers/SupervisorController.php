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
        $departments_abbreviations = $request->departments_abbreviations;
        // loop through the array of departments_abbreviations find the department_id and create a new internship_department record
        foreach ($departments_abbreviations as $department_abbreviation) {
            $department = Department::where('abbreviation', $department_abbreviation)->first();
            $internship->departments()->attach($department->id);
        }

        return response()->json($internship);
    }

}

