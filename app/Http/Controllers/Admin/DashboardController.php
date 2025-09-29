<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(): View
    {
        $data = [
            'totalUsers' => User::count(),
            'totalEstimates' => 0, // Will be implemented when PriceEstimate model is created
            'pendingBookings' => 0, // Will be implemented when Booking model is created
            'completedProjects' => 0, // Will be implemented when Project model is created
            'recentActivities' => [], // Will be implemented when Activity model is created
        ];

        return view('admin.dashboard', $data);
    }
}