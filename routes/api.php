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
// this route is returning a 401 error

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//get profile infos based on the logged in user
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'getProfile']);

// get notifications for the logged in user
Route::middleware('auth:sanctum')->get('/notifications', [NotificationController::class, 'getNotifications']);

// make all notifications as read for the logged in user
Route::middleware('auth:sanctum')->post('/notifications/markAllAsRead', [NotificationController::class, 'markAllAsRead']);


//admin routes only
Route::middleware('auth:sanctum','admin')->group(function () {
    // get all students in the same department as the logged in admin
    Route::get('/students', [AdminController::class, 'getStudents']);
    // get all interns in the same department as the logged in admin
    Route::get('/interns', [AdminController::class, 'getInterns']);

});


