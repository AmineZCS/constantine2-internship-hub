<?php

use Illuminate\Support\Facades\Route;
// users model
use App\Models\User;
// all roles models
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Internship;
use Barryvdh\DomPDF\Facade as PDF;

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
//  a route to get the profile picture of a user
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
        if ($imageName == null) {
            $path = storage_path('app/public/pictures/profile_pics/default.png');
            return response()->file($path);
        }
        $path = storage_path('app/public/pictures/profile_pics/' . $imageName);
        if (!file_exists($path)) {
            $path = storage_path('app/public/pictures/profile_pics/default.png');
        }
        return response()->file($path);
});

// a route to get the company picture
Route::get('companyPic/{companyId}', function ($companyId) {
    // get the company image path from the database
    $company = Company::where('id', $companyId)->first();
    $imageName = $company->image;
    if ($imageName == null) {
        $path = storage_path('app/public/pictures/company_pics/default.png');
        return response()->file($path);
    }
    $path = storage_path('app/public/pictures/company_pics/' . $imageName);
    if (!file_exists($path)) {
        $path = storage_path('app/public/pictures/company_pics/default.png');
    }
    return response()->file($path);
});
// a route to get internship picture
Route::get('internshipPic/{internshipId}', function ($internshipId) {
    // get the internship image path from the database
    $internship = Internship::where('id', $internshipId)->first();
    $imageName = $internship->photo;
    if ($imageName == null) {
        $path = storage_path('app/public/pictures/internship_pics/default.png');
        return response()->file($path);
    }
    $path = storage_path('app/public/pictures/internship_pics/' . $imageName);
    if (!file_exists($path)) {
        $path = storage_path('app/public/pictures/internship_pics/default.png');
    }
    return response()->file($path);
});

// Route::get('/generateQRCode/{token}', function ($token) {
//     $url = env('FRONTEND_URL') . '/certificate/' . $token;
//     $qrCode = QrCode::size(50)->generate($url);
//     return view('certificate', ['qrCode' => $qrCode, 'token' => $token]);
// });

Route::get('/generateQRCode/{token}', function ($token) {
    $url = env('FRONTEND_URL') . '/certificate/' . $token;
    $qrCode = QrCode::size(50)->generate($url);
    $html = view('certificate', ['qrCode' => $qrCode, 'token' => $token])->render();
    $pdf = PDF::loadHTML($html);
    return $pdf->stream('certificate.pdf');
});