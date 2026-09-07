<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::forUser(Auth::id())
            ->orderByDesc('created_at')
            ->paginate(20);

        // Mark all as read when viewing the list
        Notification::forUser(Auth::id())->unread()->update(['is_read' => true, 'read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        Notification::forUser(Auth::id())
            ->unread()
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    public function getUnreadCount()
    {
        $count = Notification::forUser(Auth::id())->unread()->count();

        return response()->json(['count' => $count]);
    }

    public function getDropdown()
    {
        $notifications = Notification::forUser(Auth::id())
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
        $unreadCount = Notification::forUser(Auth::id())->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }
}
