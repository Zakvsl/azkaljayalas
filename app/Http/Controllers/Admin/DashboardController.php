<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PriceEstimate;
use App\Models\SurveyBooking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(): View
    {
        // Get statistics
        $totalUsers = User::count();
        $totalEstimates = PriceEstimate::count();
        $pendingBookings = SurveyBooking::where('status', 'pending')->count();
        $completedProjects = SurveyBooking::where('status', 'completed')->count();
        
        // Get recent activities (last 5)
        $recentActivities = [];
        
        // Add recent users
        $recentUsers = User::latest()->take(2)->get();
        foreach ($recentUsers as $user) {
            $recentActivities[] = [
                'icon' => 'user-plus',
                'message' => 'New user registered: ' . $user->name,
                'time' => $user->created_at->diffForHumans(),
            ];
        }
        
        // Add recent estimates
        $recentEstimates = PriceEstimate::latest()->take(2)->get();
        foreach ($recentEstimates as $estimate) {
            $recentActivities[] = [
                'icon' => 'calculator',
                'message' => 'New price estimate requested by ' . $estimate->user->name,
                'time' => $estimate->created_at->diffForHumans(),
            ];
        }
        
        // Add recent bookings
        $recentBookings = SurveyBooking::latest()->take(1)->get();
        foreach ($recentBookings as $booking) {
            $recentActivities[] = [
                'icon' => 'calendar-check',
                'message' => 'New survey booking: ' . $booking->full_name,
                'time' => $booking->created_at->diffForHumans(),
            ];
        }
        
        // Sort by time and limit to 5
        usort($recentActivities, function($a, $b) {
            return strtotime($b['time']) <=> strtotime($a['time']);
        });
        $recentActivities = array_slice($recentActivities, 0, 5);
        
        $data = [
            'totalUsers' => $totalUsers,
            'totalEstimates' => $totalEstimates,
            'pendingBookings' => $pendingBookings,
            'completedProjects' => $completedProjects,
            'recentActivities' => $recentActivities,
        ];

        return view('admin.dashboard', $data);
    }
}