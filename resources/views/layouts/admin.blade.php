<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - AZKAL JAYA LAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: true, mobileSidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'w-64' : 'w-20'" 
            class="hidden md:flex flex-col bg-blue-900 text-white transition-all duration-300 ease-in-out">
            
            <!-- Logo & Toggle -->
            <div class="flex items-center justify-between p-4 border-b border-blue-800">
                <div x-show="sidebarOpen" class="flex items-center space-x-3">
                    <i class="fas fa-tools text-2xl"></i>
                    <span class="text-xl font-bold whitespace-nowrap">Azkal Jaya</span>
                </div>
                <div x-show="!sidebarOpen" class="flex items-center justify-center w-full">
                    <i class="fas fa-tools text-2xl"></i>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-6 space-y-2 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-home text-lg w-6"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">Dashboard</span>
                </a>

                <!-- Manage Users -->
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-users text-lg w-6"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">Manage Users</span>
                </a>

                <!-- Price Estimates -->
                <a href="{{ route('estimates.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('estimates.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-calculator text-lg w-6"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">Price Estimates</span>
                </a>

                <!-- Survey Bookings -->
                <a href="{{ route('admin.survey-bookings.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('admin.survey-bookings.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-calendar-check text-lg w-6"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">Survey Bookings</span>
                </a>

                <!-- ML Model Management -->
                <a href="{{ route('admin.ml.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('admin.ml.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-brain text-lg w-6"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">ML Model</span>
                </a>

                <!-- Profile Settings -->
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('profile.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-user-cog text-lg w-6"></i>
                    <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">Profile Settings</span>
                </a>

                <hr class="my-4 border-blue-800">

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-3 py-2.5 rounded-lg hover:bg-red-600 transition-colors text-left">
                        <i class="fas fa-sign-out-alt text-lg w-6"></i>
                        <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">Logout</span>
                    </button>
                </form>
            </nav>

            <!-- User Info -->
            <div class="p-4 border-t border-blue-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-blue-700 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user"></i>
                    </div>
                    <div x-show="sidebarOpen" class="overflow-hidden">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-blue-300 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div x-show="mobileSidebarOpen" 
             @click="mobileSidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
             style="display: none;">
        </div>

        <!-- Mobile Sidebar -->
        <aside 
            x-show="mobileSidebarOpen"
            @click.away="mobileSidebarOpen = false"
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 w-64 bg-blue-900 text-white z-50 md:hidden flex flex-col"
            style="display: none;">
            
            <!-- Logo & Close -->
            <div class="flex items-center justify-between p-4 border-b border-blue-800">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-tools text-2xl"></i>
                    <span class="text-xl font-bold">Azkal Jaya</span>
                </div>
                <button @click="mobileSidebarOpen = false" class="text-white hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-6 space-y-2 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-home text-lg w-6"></i>
                    <span class="ml-3">Dashboard</span>
                </a>

                <!-- Manage Users -->
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-users text-lg w-6"></i>
                    <span class="ml-3">Manage Users</span>
                </a>

                <!-- Price Estimates -->
                <a href="{{ route('estimates.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('estimates.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-calculator text-lg w-6"></i>
                    <span class="ml-3">Price Estimates</span>
                </a>

                <!-- Survey Bookings -->
                <a href="{{ route('admin.survey-bookings.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('admin.survey-bookings.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-calendar-check text-lg w-6"></i>
                    <span class="ml-3">Survey Bookings</span>
                </a>

                <!-- ML Model Management -->
                <a href="{{ route('admin.ml.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('admin.ml.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-brain text-lg w-6"></i>
                    <span class="ml-3">ML Model</span>
                </a>

                <!-- Profile Settings -->
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg hover:bg-blue-800 transition-colors {{ request()->routeIs('profile.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-user-cog text-lg w-6"></i>
                    <span class="ml-3">Profile Settings</span>
                </a>

                <hr class="my-4 border-blue-800">

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-3 py-2.5 rounded-lg hover:bg-red-600 transition-colors text-left">
                        <i class="fas fa-sign-out-alt text-lg w-6"></i>
                        <span class="ml-3">Logout</span>
                    </button>
                </form>
            </nav>

            <!-- User Info -->
            <div class="p-4 border-t border-blue-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-blue-700 flex items-center justify-center">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-blue-300 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Top Header -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between px-4 py-4">
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Menu Button -->
                        <button @click="mobileSidebarOpen = true" class="text-gray-600 hover:text-gray-900 md:hidden">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>

                        <!-- Desktop Sidebar Toggle -->
                        <button @click="sidebarOpen = !sidebarOpen" class="hidden md:block text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>

                        <h1 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="text-gray-600 hover:text-gray-900 relative">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                        </button>

                        <!-- User Dropdown (Desktop) -->
                        <div class="hidden md:flex items-center space-x-2 text-gray-700">
                            <i class="fas fa-user-circle text-2xl"></i>
                            <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-100 p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
