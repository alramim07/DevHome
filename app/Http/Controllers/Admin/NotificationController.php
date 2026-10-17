<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function feed()
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
            ->unread()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'body'       => $n->body,
                'url'        => $n->url,
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'count'         => $notifications->count(),
            'notifications' => $notifications,
        ]);
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
