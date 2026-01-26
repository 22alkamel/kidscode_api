<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->get();

       $notifications = $notifications->map(function($notif) {
        return [
            'id' => $notif->id,
            'message' => $notif->data['message'] ?? null, // إذا ما فيه message يرجع null
            'url' => $notif->data['url'] ?? null,         // إذا ما فيه url يرجع null
            'read_at' => $notif->read_at,
        ];
         });

        return response()->json($notifications);
    }

    // لتعليم الإشعار كمقروء
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['status' => 'success']);
    }
}
