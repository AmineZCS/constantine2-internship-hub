<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    // login and return token + role
    public function login(Request $request)
    {
         $request->validate([
        'email' => 'required|email',
        'password' => 'required',
         ]);

    $user = User::where('email', $request->email)->first();
// check if email exists
    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }
    // check if password is correct
    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Wrong password'
        ], 404);
    }
    return response()->json([
        'token' => $user->createToken($request->email)->plainTextToken,
        'role' => $user->role
    ]);
    }


    //logout and revoke token
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    //get profile informations based on the logged in user
    public function getProfile(Request $request)
    {
        $role = $request->user()->role;
        switch ($role) {
            case 'admin':
                $admin = Admin::join('users', 'users.id', '=', 'admins.id')
                    ->where('admins.id', $request->user()->id)
                    ->select('admins.*', 'users.email')
                    ->first();
                return response()->json($admin);
                break;
            case 'supervisor':
                $supervisor = Supervisor::join('users', 'users.id', '=', 'supervisors.id')
                    ->where('supervisors.id', $request->user()->id)
                    ->select('supervisors.*', 'users.email')
                    ->first();
                return response()->json($supervisor);
                break;
            case 'student':
                $student = Student::join('users', 'users.id', '=', 'students.id')
                    ->where('students.id', $request->user()->id)
                    ->select('students.*', 'users.email')
                    ->first();
                return response()->json($student);
                break;
            default:
                return response()->json(['message' => 'No role found']);
                break;
        }
    }

}
