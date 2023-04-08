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

// get all departments in an array of objects
Route::get('/departments', [UsersController::class, 'getDepartments']);


//admin routes only
Route::middleware('auth:sanctum','admin')->group(function () {
    // get all students in the same department as the logged in admin
    Route::get('/students', [AdminController::class, 'getStudents']);
    // get all interns in the same department as the logged in admin
    Route::get('/departmentInterns', [AdminController::class, 'getDepartmentInterns']);
});


// ============================================================


// student routes only
Route::middleware('auth:sanctum','student')->group(function () {
    // get all internships in the same department as the logged in student
    Route::get('/studentInterns', [StudentController::class, 'getStudentInterns']);
});

// ============================================================



// supervisor routes only
Route::middleware('auth:sanctum','supervisor')->group(function () {
    // create a new internship
    Route::post('/internships', [SupervisorController::class, 'createInternship']);
});
