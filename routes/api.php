<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\CorsMiddleware;
use App\Models\Certificate;
use App\Mail\WelcomeEmail;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//all roles can access these routes
// check the certificate token and return the certificate data if it exists (inline function)
Route::get('/certificate', function (Request $request) {
    $certificate = Certificate::where('token', $request->token)->first();
if ($certificate) {
    $student = $certificate->student;
    $internship = $certificate->internship;

        return response()->json([
            'student' => $student,
            'internship' => $internship,
        ], 200);
    } else {
        return response()->json([
            'message' => 'Certificate not found',
        ], 404);
    }
});
// getOrCreate the certificate token and return the certificate data if it exists (inline function)
Route::post('/certificate', function (Request $resuest){
    $certificate = Certificate::where('student_id', $resuest->student_id)
        ->where('internship_id', $resuest->internship_id)
        ->first();

    if (!$certificate) {
        $certificate = new Certificate();
        $certificate->student_id = $resuest->student_id;
        $certificate->internship_id = $resuest->internship_id;
        $certificate->save();
        return $certificate;
    }else {
        return $certificate;
    }
});
    
// test the email sender by sending a welcome email to the logged in user


// login and return token
Route::post('/login', [AuthController::class, 'login']);

//logout and revoke token
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//get profile infos based on the logged in user
Route::middleware('auth:sanctum')->get('/user', [UsersController::class, 'getProfile']);

// get notifications for the logged in user
Route::middleware('auth:sanctum')->get('/notifications', [NotificationController::class, 'getNotifications']);

// make all notifications as read for the logged in user
Route::middleware('auth:sanctum')->post('/notifications/markAllAsRead', [NotificationController::class, 'markAllAsRead']);

// accept student id and download the student's cv from the storage/app/public/cvs/profile_cvs/user_id
Route::middleware('auth:sanctum')->get('/downloadCV', [UsersController::class, 'downloadCV']);
// update the logged in users's profile picture
Route::middleware('auth:sanctum')->post('/updateProfilePicture', [UsersController::class, 'updateProfilePicture']);






// get all departments in an array of objects
Route::get('/departments', [UsersController::class, 'getDepartments']);

// get all companies in an array of objects
Route::get('/companies', [UsersController::class, 'getCompanies']);

// SignUp

    // supervisor signup
    Route::post('/supervisorSignup', [SupervisorController::class, 'signUp']);

    // student signup
    Route::post('/studentSignup', [StudentController::class, 'signUp']);

    // admin signup
    Route::post('/adminSignup', [AdminController::class, 'signUp']);


// ============================================================



//admin routes only
Route::middleware('auth:sanctum','admin')->group(function () {
    // get all students in the same department as the logged in admin
    Route::get('/students', [AdminController::class, 'getStudents']);
    // get all interns in the same department as the logged in admin
    Route::get('/departmentInterns', [AdminController::class, 'getDepartmentInterns']);
    // get all admin feedbacks
    Route::get('/adminFeedbacks', [AdminController::class, 'getAdminFeedbacks']);
    // get all applications in the same department as the logged in admin
    Route::get('/departmentApplications', [AdminController::class, 'getDepartmentApplications']);
    // create a new admin feedback
    Route::post('/adminFeedbacks', [AdminController::class, 'createAdminFeedback']);
    // approve an application
    Route::post('/adminApprove', [AdminController::class, 'approveApplication']);
    // reject an application
    Route::post('/adminReject', [AdminController::class, 'rejectApplication']);
    // accept supervisor account
    Route::post('/adminAcceptSupervisor', [AdminController::class, 'acceptSupervisor']);
    // reject supervisor account
    Route::post('/adminRejectSupervisor', [AdminController::class, 'rejectSupervisor']);
    // get a list of all supervisors
    Route::get('/supervisors', [AdminController::class, 'getSupervisors']);
});


// ============================================================


// student routes only
Route::middleware('auth:sanctum','student')->group(function () {
    // get all internships in the same department as the logged in student
    Route::get('/studentInterns', [StudentController::class, 'getStudentInterns']);
    // apply for an internship
    Route::post('/apply', [StudentController::class, 'applyForInternship']);
    // get all applications for the logged in student
    Route::get('/applications', [StudentController::class, 'getStudentApplications']);
    // get all feedbacks for the given application
    Route::get('/applicationFeedbacks', [StudentController::class, 'getApplicationFeedbacks']);
    // upload the student's CV 
    Route::post('/uploadCV', [StudentController::class, 'uploadCV']);
    // get student Evaluation
    Route::get('/studentEvaluation', [StudentController::class, 'getStudentEvaluation']);
    // get student attendance
    Route::get('/studentAttendance', [StudentController::class, 'getStudentAttendance']);
    // create internship
    Route::post('/studentNewInternship', [StudentController::class, 'createInternship']);
    });

// ============================================================



// supervisor routes only
Route::middleware('auth:sanctum','supervisor')->group(function () {
    // create a new internship
    Route::post('/internships', [SupervisorController::class, 'createInternship']);
    // get all internships created by the logged in supervisor
    Route::get('/internships', [SupervisorController::class, 'getSupervisorInterns']);
    // get all supervisor feedbacks
    Route::get('/supervisorFeedbacks', [SupervisorController::class, 'getSupervisorFeedbacks']);
    // create a new supervisor feedback
    Route::post('/supervisorFeedbacks', [SupervisorController::class, 'createSupervisorFeedback']);
    // mark attendance for the logged in supervisor
    Route::post('/markAttendance', [SupervisorController::class, 'markAttendance']);
    // get all attendance for the logged in supervisor
    Route::get('/attendance', [SupervisorController::class, 'getAttendance']);
    // get all internship applications for the logged in supervisor
    Route::get('/supervisorApplications',[SupervisorController::class, 'getApplications']);
    // accept an internship application
    Route::post('/supervisorAcceptApplication', [SupervisorController::class, 'acceptApplication']);
    // reject an internship application
    Route::post('/supervisorRejectApplication', [SupervisorController::class, 'rejectApplication']);                                                                                                        
    // get all evaluations for the logged in supervisor
    Route::get('/evaluations', [SupervisorController::class, 'getEvaluations']);
    // edit an evaluation
    Route::post('/editEvaluation', [SupervisorController::class, 'editEvaluation']);
    // create a new evaluation
    Route::post('/createEvaluation', [SupervisorController::class, 'createEvaluation']);
    // get all students who applied to internships in the logged in supervisor's company and are accepted by the admin and the supervisor
    Route::get('/acceptedStudents', [SupervisorController::class, 'getAcceptedStudents']);
    // get supervisor's students to mark atttendance for
    Route::get('/supervisorStudents', [SupervisorController::class, 'getSupervisorStudents']);
});
