<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id());

        // Filter by type
        if ($request->has('type') && $request->type !== '') {
            $query->ofType($request->type);
        }

        // Filter by read status
        if ($request->has('read_status')) {
            if ($request->read_status === 'unread') {
                $query->unread();
            } elseif ($request->read_status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        $notifications = $query->recent()->paginate(15);
        
        $unreadCount = Notification::where('user_id', Auth::id())->unread()->count();
        
        $stats = [
            'total' => Notification::where('user_id', Auth::id())->count(),
            'unread' => $unreadCount,
            'booking' => Notification::where('user_id', Auth::id())->ofType('booking')->count(),
            'survey' => Notification::where('user_id', Auth::id())->ofType('survey')->count(),
            'price_offer' => Notification::where('user_id', Auth::id())->ofType('price_offer')->count(),
            'order_progress' => Notification::where('user_id', Auth::id())->ofType('order_progress')->count(),
            'payment' => Notification::where('user_id', Auth::id())->ofType('payment')->count(),
        ];

        return view('notifications.index', compact('notifications', 'stats', 'unreadCount'));
    }

    /**
     * Mark single notification as read
     */
    public function markAsRead(Notification $notification)
    {
        // Check authorization
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandai sebagai sudah dibaca.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sebagai sudah dibaca.');
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification)
    {
        // Check authorization
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification deleted',
            ]);
        }

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNotNull('read_at')
            ->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'All read notifications deleted',
            ]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi yang sudah dibaca berhasil dihapus.');
    }

    /**
     * Get unread notifications count (for header badge)
     */
    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())->unread()->count();
        
        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * Get latest notifications (for dropdown)
     */
    public function latest()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->recent()
            ->take(5)
            ->get();

        $unreadCount = Notification::where('user_id', Auth::id())->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }
}
