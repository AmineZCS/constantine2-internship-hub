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
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'getProfile']);


// Route::middleware('auth:sanctum','student')->get('/user', function (Request $request) {
//     return $request->user();
// });
