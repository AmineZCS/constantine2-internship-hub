<?php

use Illuminate\Support\Facades\Route;
// users model
use App\Models\User;
// all roles models
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Admin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('profilePic/{userId}', function ($userid) {
    
        // get the user role
        $role = User::where('id', $userid)->first()->role;
        // get the image path from the database based on the user role
        switch ($role) {
            case 'admin':
                $admin = Admin::where('id', $userid)->first();
                $imageName = $admin->image;
                break;
            case 'supervisor':
                $supervisor = Supervisor::where('id', $userid)->first();
                $imageName = $supervisor->image;
                break;
            case 'student':
                $student = Student::where('id', $userid)->first();
                $imageName = $student->photo_path;
                break;
            default:
                return response()->json(['message' => 'No role found']);
                break;
        }
        $path = storage_path('app/public/pictures/profile_pics/' . $imageName);
    
        if (!file_exists($path)) {
            abort(404);
        }
    
        return response()->file($path);
});
