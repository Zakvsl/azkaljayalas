<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    /**
     * Display admin notifications (user bookings, payments, etc)
     */
    public function index(Request $request)
    {
        // Get notifications for current admin user
        $query = Notification::where('user_id', Auth::id());

        // Filter by type
        if ($request->has('type') && $request->type !== '') {
            $query->ofType($request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'new') {
                $query->where('created_at', '>=', now()->subDays(7));
            }
        }

        $notifications = $query->with('surveyBooking.user')->recent()->paginate(15);
        
        $stats = [
            'total' => Notification::where('user_id', Auth::id())->count(),
            'new_bookings' => Notification::where('user_id', Auth::id())->ofType('booking')->where('created_at', '>=', now()->subDays(7))->count(),
            'new_payments' => Notification::where('user_id', Auth::id())->ofType('payment')->where('created_at', '>=', now()->subDays(7))->count(),
            'booking' => Notification::where('user_id', Auth::id())->ofType('booking')->count(),
            'payment' => Notification::where('user_id', Auth::id())->ofType('payment')->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Get unread count for admin notification bell
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications for dropdown
     */
    public function getRecent()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->with('surveyBooking.user')
            ->recent()
            ->limit(5)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'user_name' => $notification->surveyBooking?->user?->name ?? 'System',
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                ];
            });

        return response()->json($notifications);
    }
}
