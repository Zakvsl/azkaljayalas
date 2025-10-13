<nav class="bg-white shadow-sm fixed top-0 left-0 right-0 w-full z-50" x-data="{ navOpen: false }">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <a href="/" class="text-xl font-bold text-blue-700">AZKAL JAYA LAS</a>

            <!-- Centered Desktop Links -->
            <div class="hidden md:flex space-x-8">
                <a href="/" class="text-gray-600 hover:text-blue-700">Home</a>
                <a href="#layanan" class="text-gray-600 hover:text-blue-700">Layanan</a>
                <a href="#tentang" class="text-gray-600 hover:text-blue-700">Tentang</a>
                <a href="https://wa.me/6285292674783" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-blue-700">Kontak</a>
            </div>

            <!-- Right-side Auth Links -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-600 hover:text-blue-700 focus:outline-none">
                            <span class="mr-2">{{ Auth::user()->name }}</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="origin-top-right absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 z-50"
                             style="display: none;">
                            
                            <!-- User Info Section -->
                            <div class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-1">
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                        Dashboard Admin
                                    </a>
                                @endif
                                
                                <a href="{{ route('profile.edit') }}" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile Settings
                                </a>
                                
                                <a href="{{ route('password.request') }}" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                    Reset Password
                                </a>
                            </div>

                            <!-- Logout Section -->
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-700">Log in</a>
                    <a href="{{ route('register') }}" class="bg-blue-700 text-white px-6 py-2 rounded-lg hover:bg-blue-800">Sign Up</a>
                @endauth
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="md:hidden">
                <button @click="navOpen = !navOpen" class="text-gray-600 hover:text-blue-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path :class="{'hidden': navOpen, 'inline-flex': !navOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                         <path :class="{'hidden': !navOpen, 'inline-flex': navOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': navOpen, 'hidden': !navOpen}" class="md:hidden bg-white border-t border-gray-200">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <a href="/" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">Home</a>
            <a href="#layanan" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">Layanan</a>
            <a href="#tentang" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">Tentang</a>
            <a href="https://wa.me/6285292674783" target="_blank" rel="noopener noreferrer" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">Kontak</a>
        </div>
        
        <!-- Mobile Auth Links -->
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="px-5 space-y-3">
            @auth
                <!-- User Info Mobile -->
                <div class="flex items-center px-3 py-2">
                    <div>
                        <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">
                        Dashboard Admin
                    </a>
                @endif
                <a href="{{ route('profile.edit') }}" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">Profile Settings</a>
                <a href="{{ route('password.request') }}" class="block text-gray-600 hover:bg-gray-100 hover:text-blue-700 px-3 py-2 rounded-md">Reset Password</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-red-600 hover:bg-red-50 px-3 py-2 rounded-md">Log Out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block bg-blue-700 text-white text-center px-6 py-2 rounded-lg hover:bg-blue-800">Log in</a>
                <a href="{{ route('register') }}" class="block bg-gray-200 text-gray-700 text-center px-6 py-2 rounded-lg hover:bg-gray-300 mt-2">Sign Up</a>
            @endauth
            </div>
        </div>
    </div>
</nav>
