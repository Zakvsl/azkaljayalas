<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details - AZKAL JAYA LAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-blue-700">AZKAL JAYA LAS - Admin</a>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-blue-700">Dashboard</a>
                    <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-blue-700">Users</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8">
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Users
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-blue-700 text-white">
                <h1 class="text-2xl font-bold">User Details</h1>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ID</label>
                        <p class="text-gray-900">{{ $user->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <p class="text-gray-900">{{ $user->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <p class="text-gray-900">{{ $user->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <p class="text-gray-900">{{ $user->phone_number ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Verified</label>
                        <p class="text-gray-900">
                            @if($user->email_verified_at)
                                <span class="text-green-600">✓ Verified on {{ $user->email_verified_at->format('d M Y H:i') }}</span>
                            @else
                                <span class="text-red-600">✗ Not verified</span>
                            @endif
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <p class="text-gray-900">{{ $user->address ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Registered At</label>
                        <p class="text-gray-900">{{ $user->created_at->format('d M Y H:i:s') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Updated</label>
                        <p class="text-gray-900">{{ $user->updated_at->format('d M Y H:i:s') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex space-x-4">
                    <a href="{{ route('admin.users.edit', $user) }}" class="bg-blue-700 text-white px-6 py-2 rounded-lg hover:bg-blue-800">
                        Edit User
                    </a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">
                            Delete User
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- User Statistics -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Price Estimates</h3>
                <p class="text-3xl font-bold text-blue-700">{{ $user->priceEstimates->count() }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Survey Bookings</h3>
                <p class="text-3xl font-bold text-blue-700">{{ $user->surveyBookings->count() }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Account Age</h3>
                <p class="text-3xl font-bold text-blue-700">{{ $user->created_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>
</body>
</html>
