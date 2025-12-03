@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">📊 Laporan Bulanan</h1>
            <p class="text-gray-600 mt-2">Monitoring kinerja dan statistik bulanan</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter & Generate Report -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter Tahun</label>
                    <select id="yearFilter" class="w-full border border-gray-300 rounded-lg px-4 py-2" onchange="filterReports()">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter Bulan (Opsional)</label>
                    <select id="monthFilter" class="w-full border border-gray-300 rounded-lg px-4 py-2" onchange="filterReports()">
                        <option value="">Semua Bulan</option>
                        @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $i => $monthName)
                            <option value="{{ $i + 1 }}" {{ request('month') == ($i + 1) ? 'selected' : '' }}>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <form action="{{ route('admin.reports.generate') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            🔄 Generate Laporan Baru
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        @if($reports->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Total Bookings</p>
                        <p class="text-3xl font-bold mt-1">{{ $reports->sum('total_bookings') }}</p>
                    </div>
                    <div class="text-5xl opacity-50">📅</div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Orders Selesai</p>
                        <p class="text-3xl font-bold mt-1">{{ $reports->sum('completed_orders') }}</p>
                    </div>
                    <div class="text-5xl opacity-50">✅</div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Total Revenue</p>
                        <p class="text-2xl font-bold mt-1">Rp {{ number_format($reports->sum('total_revenue'), 0, ',', '.') }}</p>
                    </div>
                    <div class="text-5xl opacity-50">💰</div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm">Conversion Rate</p>
                        <p class="text-3xl font-bold mt-1">
                            @php
                                $totalBookings = $reports->sum('total_bookings');
                                $completedOrders = $reports->sum('completed_orders');
                                $conversionRate = $totalBookings > 0 ? round(($completedOrders / $totalBookings) * 100, 1) : 0;
                            @endphp
                            {{ $conversionRate }}%
                        </p>
                    </div>
                    <div class="text-5xl opacity-50">📈</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Reports Table -->
        @if($reports->isEmpty())
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Laporan</h3>
                <p class="text-gray-500 mb-4">Klik tombol "Generate Laporan Baru" untuk membuat laporan bulanan</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Bookings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confirmed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cancelled</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conversion</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">
                                        {{ ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'][$report->month] }} {{ $report->year }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $report->total_bookings }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                    {{ $report->confirmed_bookings }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                    {{ $report->cancelled_bookings }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                                    {{ $report->completed_orders }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($report->total_revenue, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $convRate = $report->total_bookings > 0 ? round(($report->completed_orders / $report->total_bookings) * 100, 1) : 0;
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                        {{ $convRate >= 70 ? 'bg-green-100 text-green-800' : ($convRate >= 40 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $convRate }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function filterReports() {
    const year = document.getElementById('yearFilter').value;
    const month = document.getElementById('monthFilter').value;
    
    let url = '{{ route("admin.reports.index") }}?year=' + year;
    if (month) {
        url += '&month=' + month;
    }
    
    window.location.href = url;
}
</script>
@endsection
