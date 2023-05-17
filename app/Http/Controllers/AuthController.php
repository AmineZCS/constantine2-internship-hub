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

// generate a documentation for this controller
/**
 * @group Authentication
 *
 * APIs for authentication
 */



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
    // check if the logged in user is a supervisor , if yes for the account status (accepted or pending)
    if($user->role == 'supervisor'){
        $supervisor = Supervisor::where('id', $user->id)->first();
        if($supervisor->status == 'pending'){
            return response()->json([
                'message' => 'Your account is waiting for approval'
            ], 404);
        }
        else if($supervisor->status == 'rejected'){
            return response()->json([
                'message' => 'Your account was rejected'
            ], 404);
        }
    }

    // return token and role
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

}
