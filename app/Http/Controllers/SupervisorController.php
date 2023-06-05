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
use App\Models\Notification;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use App\Mail\StudentApplicationDeclinedEmail;
// str
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
            // company details
            'company_name' => 'required',
            'company_email' => 'required|email|unique:companies,email',
            'company_phone_number' => 'nullable',
            'company_address' => 'nullable',
            'company_bio' => 'nullable',
        ]);
        // create a company record with the company details from the request
        $company = Company::create([
            'name' => $request->company_name,
            'email' => $request->company_email,
            'phone_number' => $request->company_phone_number,
            'address' => $request->company_address,
            'bio' => $request->company_bio,
        ]);
        $imageName = $company->id.'.'.$request->file('company_image')->extension();
        $request->file('company_image')->move(storage_path('app/public/pictures/company_pics/'), $imageName);
        $company->image = $imageName;
        $company->save();

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
            'location' => $request->location,
            'phone_number' => $request->user_phone_number,
            'bio' => $request->user_bio,
        ]);

        $user = User::where('id', $user->id)->first();
        $user_info = Supervisor::where('id', $user->id)->first();
        $imageName = $user->id.'.'.$request->file('user_image')->extension();
        $request->file('user_image')->move(storage_path('app/public/pictures/profile_pics/'), $imageName);
        $user_info->image = $imageName;
        $user_info->save();
        Mail::to($user->email)->send(new WelcomeEmail($user, $user_info));
        return response()->json([
            'token' => $user->createToken($request->email)->plainTextToken,
            'role' => $user->role,
            'message' => 'Welcome Email sent successfully'
        ],200);
        
    }
    
    public function getAcceptedStudents(Request $request)
    {
        // Get the logged-in supervisor
        $supervisor = $request->user();
    
        // Get the supervisor's internship
        $internship = Internship::where('supervisor_id', $supervisor->id)->first();
    
        // Get the students who applied to the internship and don't have a record in the evaluations table
        $students = $internship->applications()
            ->where('admin_status', 'approved') // Filter by admin acceptance
            ->where('supervisor_status', 'approved') // Filter by supervisor acceptance
            ->whereNotIn('student_id', function($query) {
                $query->select('student_id')
                      ->from('evaluations');
            }) // Filter by students without evaluations
            ->with('student') // Eager load the student relationship
            ->get()
            ->pluck('student'); // Get only the students
    
        return $students;
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
        $imageName = $internship->id.'.'.$request->file('image')->extension();
        $request->file('image')->move(storage_path('app/public/pictures/internship_pics/'), $imageName);
        $internship->photo = $imageName;
        $internship->save();
       
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
    public function markAttendance(Request $request)
    {
        $user = $request->user();
        $internshipid = Internship::where('supervisor_id', $user->id)->first()->id;
    
        foreach ($request->student_ids as $student_id) {
            $attendance = Attendance::where('student_id', $student_id)->where('date', $request->date)->first();
            if ($attendance) {
                $attendance->is_present = $request->is_present;
                $attendance->save();
            } else {
                $attendance = new Attendance();
                $attendance->student_id = $student_id;
                $attendance->internship_id = $internshipid;
                $attendance->date = $request->date;
                $attendance->is_present = $request->is_present;
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
    $feedback_message = Feedback::where('id', $request->feedback_id)->select('message')->first();
       
    if ($internship_supervisor) {
            $application->supervisor_status = 'rejected';
            $feedback_application = new FeedbackApplication();
            $feedback_application->application_id = $application->id;
            $feedback_application->feedback_id = $request->feedback_id;
            $feedback_application->save();
            $application->save();
            // create a new notification for the student
            $notification = new Notification();
            $notification->user_id = $application->student_id;
            $notification->title = 'Application Rejected';
            $notification->message = 'Your application for ' . $internship->position . ' internship has been rejected by the supervisor';
            $notification->save();
            // send email to student with the feedback (use )
                // create a data array to pass to the email view (it has fname and lname of student , feedback message , internship position , supervisor name)
            $student = Student::where('id', $application->student_id)->first();
            $data = [
                    'fname' => $student->fname,
                    'lname' => $student->lname,
                    'feedback' => $feedback_message->message,
                    'position' => $internship->position,
                    'name' => $supervisor->fname . ' ' . $supervisor->lname,
                ];
            $receiver = User::where('id', $student->id)->first();
            // send email to student
            Mail::to($receiver->email)->send(new StudentApplicationDeclinedEmail($data));

            return response()->json($application);
    } else {
        return response()->json(['error' => 'Application is not for an internship you supervise'], 400);
    }
    }
    // get all student evaluations for the logged in supervisor's internship (include student informations)
    
    public function getEvaluations(Request $request)
    {
    
    $user = $request->user();
    $supervisor = Supervisor::where('id', $user->id)->first();
    $internship = Internship::where('supervisor_id', $supervisor->id)->first();
    $evaluations = Evaluation::where('supervisor_id', $supervisor->id)
        ->with(['student' => function ($query) {
            $query->with(['user' => function ($query) {
                $query->select('id', 'email');
            }, 'department' => function ($query) {
                $query->select('id', 'abbreviation');
            }]);
        }])
        ->get();
    return response()->json($evaluations);

    }

    // edit the evaluation of a student (change the evaluation's general skills initiative imagination knowledge global_appreciation)
    public function editEvaluation(Request $request)
    {
        // validate inputs
        $this->validate($request, [
            'evaluation_id' => 'required|integer',
            'general' => 'required|integer',
            'initiative' => 'required|integer',
            'imagination' => 'required|integer',
            'skills' => 'required|integer',
            'knowledge' => 'required|integer',
            'global_appreciation' => 'required|string',
        ]);
        $evaluation = Evaluation::where('id', $request->evaluation_id)->first();
        $evaluation->general = $request->general;
        $evaluation->initiative = $request->initiative;
        $evaluation->imagination = $request->imagination;
        $evaluation->knowledge = $request->knowledge;
        $evaluation->skills = $request->skills;
        $evaluation->global_appreciation = $request->global_appreciation;
        // caluclate the total mark
        $total = $request->general + $request->initiative + $request->imagination  + $request->knowledge + $request->skills;
        $evaluation->total_mark = $total;
        $evaluation->save();
        return response()->json(['message' => 'success']);

    }
    // create a new evaluation (pass student id and evaluation marks) and calculate the total_mark
    public function createEvaluation(Request $request){
         // validate inputs
         $this->validate($request, [
            'student_id' => 'required|integer',
            'general' => 'required|integer',
            'initiative' => 'required|integer',
            'imagination' => 'required|integer',
            'skills' => 'required|integer',
            'knowledge' => 'required|integer',
            'global_appreciation' => 'required|string',
        ]);
        $evaluation = new Evaluation();
        $evaluation->student_id = $request->student_id;
        $evaluation->supervisor_id = $request->user()->id;
        $evaluation->general = $request->general;
        $evaluation->initiative = $request->initiative;
        $evaluation->imagination = $request->imagination;
        $evaluation->knowledge = $request->knowledge;
        $evaluation->skills = $request->skills;
        $evaluation->global_appreciation = $request->global_appreciation;
         // caluclate the total mark
         $total = $request->general + $request->initiative + $request->imagination  + $request->knowledge + $request->skills;
         $evaluation->total_mark = $total;
         $evaluation->save();
         return response()->json(['message' => 'success']);

    }
    // get students to mark new attendance 
    public function getSupervisorStudents(Request $request)
{
    // Get the logged-in supervisor
    $supervisor = $request->user();

    // Get the supervisor's internship
    $internship = Internship::where('supervisor_id', $supervisor->id)->first();

    // Get the students who applied to the internship and have been approved by the admin and supervisor
    $students = $internship->applications()
        ->where('admin_status', 'approved') // Filter by admin acceptance
        ->where('supervisor_status', 'approved') // Filter by supervisor acceptance
        ->with('student') // Eager load the student relationship
        ->get()
        ->pluck('student'); // Get only the students

    return $students;
}

public function genererPdf(Request $request)
    {
        $array = DB::table('ETUDIANT')
            ->where('ETUDIANT.id_Etud', '=', $request->id_Etud)
            ->join('STAGE', 'ETUDIANT.id_Etud', '=', 'STAGE.id_Etud')
            ->join('OFFRE', 'OFFRE.id_Offre', '=', 'STAGE.id_Offre')
            ->join('RESPONSABLE', 'RESPONSABLE.id_Resp', '=', 'OFFRE.id_Resp')
            ->join('ENTREPRISE', 'OFFRE.id_Entreprise', '=', 'ENTREPRISE.id_Entreprise')
            ->select([
                'ETUDIANT.id_Etud',
                'nom_Etud',
                'pre_Etud',
                'dateDeb',
                'dateFin',
                'dateNaiss',
                'lieuNaiss',
                'specialite',
                'nom_Resp',
                'pre_Resp',
                'addr_Entreprise',
                'theme',
                'diplome',
                'nom_Entreprise'
            ])
            ->get();
        $array = json_decode($array, true);
            
        $certificate = Certificate::where('id_Etud', $request->id_Etud)
        ->where('id_Stage', $resuest->id_Stage)
        ->first();

    if (!$certificate) {
        $certificate = new Certificate();
        $certificate->id_Etud = $resuest->id_Etud;
        $certificate->id_Stage = $resuest->id_Stage;
        $certificate->save();
    }

        $data_array = [
            'nom' => $array[0]['nom_Etud'],
            'prenom' => $array[0]['pre_Etud'],
            'theme' => $array[0]['theme'],
            'dateDeb' => $array[0]['dateDeb'],
            'dateFin' => $array[0]['dateFin'],
            'dateNaiss' => $array[0]['dateNaiss'],
            'lieuNaiss' => $array[0]['lieuNaiss'],
            'specialite' => $array[0]['specialite'],
            'diplome' => $array[0]['diplome'],
            'nom_Resp' => $array[0]['nom_Resp'],
            'pre_Resp' => $array[0]['pre_Resp'],
            'addr_Entreprise' => $array[0]['addr_Entreprise'],
            'nom_Entreprise' => $array[0]['nom_Entreprise'],
            'date' => $currentDateTime = Carbon::now()->format('Y-m-d'),
            'token' => $certificate->token,
        ];


        $pdf = PDF::loadView('pdf', $data_array);
        // Output the generated PDF to Browser
        Notification::insert([
            'destinataire' => 'etudiant',
            'id_Destinataire' => $array[0]['id_Etud'],
            'message' => 'votre attestation est prete'
        ]);



        return $pdf->stream();

    }
}