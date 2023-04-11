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
    // get all default suppervisor feedbacks
    public function getSupervisorDefaultFeedbacks (Request $request)
    {
        $feedbacks = Feedback::where('feedback_type', 'supervisor')->where('is_default', true)->get();
        return response()->json($feedbacks);
    }
    


}

