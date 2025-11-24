<nav class="bg-white shadow-sm sticky top-0 left-0 right-0 w-full z-50" x-data="{ navOpen: false, searchOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-4 gap-4">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="/" class="text-xl font-bold text-orange-600">AZKAL JAYA LAS</a>
            </div>

            <!-- Search Bar (Desktop) -->
            <div class="hidden md:flex flex-1 max-w-2xl mx-4">
                <div class="relative w-full">
                    <input
                        type="text"
                        placeholder="Cari layanan konstruksi besi..."
                        class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                    />
                    <button class="absolute right-2 top-1/2 -translate-y-1/2 bg-orange-500 hover:bg-orange-600 text-white p-2 rounded-md transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Right Menu -->
            <div class="hidden md:flex items-center gap-4">
                @auth
                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 text-gray-700 hover:text-orange-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="hidden lg:inline">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
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
                             class="origin-top-right absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100"
                             style="display: none;">
                            
                            <div class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>

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
                            </div>

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
                    <a href="{{ route('login') }}" class="flex items-center gap-2 text-gray-700 hover:text-orange-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="hidden lg:inline">Masuk</span>
                    </a>
                @endauth
            </div>

            <!-- Mobile Icons -->
            <div class="md:hidden flex items-center gap-3">
                <button @click="searchOpen = !searchOpen" class="text-gray-700 hover:text-orange-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                <button @click="navOpen = !navOpen" class="text-gray-700 hover:text-orange-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path :class="{'hidden': navOpen, 'inline-flex': !navOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                         <path :class="{'hidden': !navOpen, 'inline-flex': navOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Navigation Menu (Desktop) -->
        <nav class="hidden md:block border-t border-gray-200">
            <ul class="flex items-center gap-8 py-3 overflow-x-auto">
                <li><a href="{{ route('home') }}" class="text-gray-700 hover:text-orange-500 whitespace-nowrap transition-colors font-medium">Beranda</a></li>
                <li><a href="{{ route('portfolio.index', ['category' => 'semua']) }}" class="text-gray-700 hover:text-orange-500 whitespace-nowrap transition-colors font-medium">Portfolio</a></li>
                <li><a href="/#categories" class="text-gray-700 hover:text-orange-500 whitespace-nowrap transition-colors font-medium">Kategori</a></li>
                <li><a href="{{ route('estimates.create') }}" class="text-gray-700 hover:text-orange-500 whitespace-nowrap transition-colors font-medium">Estimasi Harga</a></li>
                <li><a href="/#layanan" class="text-gray-700 hover:text-orange-500 whitespace-nowrap transition-colors font-medium">Semua Layanan</a></li>
                <!-- <li><a href="/#promo" class="text-orange-500 whitespace-nowrap font-medium">Promo</a></li> -->
                <li><a href="/#contact" class="text-gray-700 hover:text-orange-500 whitespace-nowrap transition-colors font-medium">Kontak</a></li>
            </ul>
        </nav>
    </div>

    <!-- Mobile Search Bar -->
    <div x-show="searchOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="md:hidden px-4 pb-3 border-t border-gray-200"
         style="display: none;">
        <div class="relative">
            <input
                type="text"
                placeholder="Cari layanan..."
                class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
            />
            <button class="absolute right-2 top-1/2 -translate-y-1/2 bg-orange-500 hover:bg-orange-600 text-white p-2 rounded-md transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': navOpen, 'hidden': !navOpen}" class="md:hidden bg-white border-t border-gray-200">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <a href="{{ route('home') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-orange-500 px-3 py-2 rounded-md font-medium">Beranda</a>
            <a href="{{ route('portfolio.index', ['category' => 'semua']) }}" class="block text-gray-700 hover:bg-gray-100 hover:text-orange-500 px-3 py-2 rounded-md font-medium">Portfolio</a>
            <a href="/#categories" class="block text-gray-700 hover:bg-gray-100 hover:text-orange-500 px-3 py-2 rounded-md font-medium">Kategori</a>
            <a href="{{ route('estimates.create') }}" class="block text-gray-700 hover:bg-gray-100 hover:text-orange-500 px-3 py-2 rounded-md font-medium">Estimasi Harga</a>
            <a href="/#layanan" class="block text-gray-700 hover:bg-gray-100 hover:text-orange-500 px-3 py-2 rounded-md font-medium">Layanan</a>
            <a href="/#promo" class="block text-orange-500 hover:bg-orange-50 px-3 py-2 rounded-md font-medium">Promo</a>
            <a href="/#contact" class="block text-gray-700 hover:bg-gray-100 hover:text-orange-500 px-3 py-2 rounded-md font-medium">Kontak</a>
        </div>
        
        @auth
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="px-5 space-y-3">
                <div class="flex items-center px-3 py-2">
                    <div>
                        <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md">Dashboard Admin</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md">Profile Settings</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-red-600 hover:bg-red-50 px-3 py-2 rounded-md">Log Out</button>
                </form>
            </div>
        </div>
        @else
        <div class="pt-4 pb-3 border-t border-gray-200 px-5 space-y-2">
            <a href="{{ route('login') }}" class="block bg-orange-500 text-white text-center px-6 py-2.5 rounded-lg hover:bg-orange-600 font-medium">Log in</a>
            <a href="{{ route('register') }}" class="block bg-gray-200 text-gray-700 text-center px-6 py-2.5 rounded-lg hover:bg-gray-300 font-medium">Sign Up</a>
        </div>
        @endauth
    </div>
</nav>
