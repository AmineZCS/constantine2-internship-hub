<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    //get notifications of the logged in user
    public function getNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::join('users', 'users.id', '=', 'notifications.user_id')
                ->where('users.id', $user->id)
                ->select('notifications.*')
                ->get();
            return response()->json($notifications);
    }

    //mark all notifications as read for the logged in user
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();


        $notifications = Notification::where('user_id', $user->id)
        ->where('read', false)
        ->get();
        foreach ($notifications as $notification) {
            $notification->read = true;
            $notification->save();
        }
        return response()->json(['message' => 'All notifications marked as read']);
    }


}
