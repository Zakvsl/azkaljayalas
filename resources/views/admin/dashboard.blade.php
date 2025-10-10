@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalUsers ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Registered accounts</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Estimates -->
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Estimates</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $totalEstimates ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Price requests</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calculator text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Bookings -->
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Bookings</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $pendingBookings ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Survey appointments</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-check text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Completed Projects -->
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $completedProjects ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Finished projects</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                Quick Actions
            </h2>
            
            <div class="space-y-3">
                <a href="{{ route('admin.users.create') }}" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-plus text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Add New User</p>
                        <p class="text-xs text-gray-600">Create new account</p>
                    </div>
                </a>

                <a href="{{ route('admin.survey-bookings.index') }}" class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-calendar text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">View Bookings</p>
                        <p class="text-xs text-gray-600">Manage survey schedules</p>
                    </div>
                </a>

                <a href="{{ route('admin.ml.index') }}" class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-brain text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Train ML Model</p>
                        <p class="text-xs text-gray-600">Update prediction model</p>
                    </div>
                </a>

                <a href="{{ route('estimates.index') }}" class="flex items-center p-3 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors group">
                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-invoice text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">View Estimates</p>
                        <p class="text-xs text-gray-600">Check price requests</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-history text-blue-500 mr-2"></i>
                Recent Activity
            </h2>
            
            <div class="space-y-4">
                @if(isset($recentActivities) && count($recentActivities) > 0)
                    @foreach($recentActivities as $activity)
                    <div class="flex items-start space-x-3 pb-3 border-b border-gray-100 last:border-0">
                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-{{ $activity['icon'] ?? 'circle' }} text-gray-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p>No recent activity</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Monthly Statistics -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-chart-line text-green-500 mr-2"></i>
                Monthly Overview
            </h2>
            <div class="h-64 flex items-center justify-center text-gray-400">
                <div class="text-center">
                    <i class="fas fa-chart-bar text-6xl mb-3"></i>
                    <p>Chart will be displayed here</p>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-purple-500 mr-2"></i>
                Status Distribution
            </h2>
            <div class="h-64 flex items-center justify-center text-gray-400">
                <div class="text-center">
                    <i class="fas fa-chart-pie text-6xl mb-3"></i>
                    <p>Chart will be displayed here</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
