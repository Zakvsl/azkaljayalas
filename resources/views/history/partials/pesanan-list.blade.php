<div class="space-y-4">
    @foreach($data as $order)
        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $order->order_number }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $order->surveyBooking->project_type ?? 'Proyek' }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $order->status_badge['class'] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $order->status_badge['text'] ?? ucfirst($order->status) }}
                </span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <p class="text-xs text-gray-500">Total Harga</p>
                    <p class="font-semibold text-gray-800">Rp {{ number_format($order->final_price, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Progress</p>
                    <p class="font-medium text-gray-800">{{ $order->progress_percentage }}%</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Status</p>
                    <p class="font-medium text-gray-800">{{ $order->current_stage }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Tanggal Order</p>
                    <p class="font-medium text-gray-800">{{ $order->created_at->format('d M Y') }}</p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full transition-all" style="width: {{ $order->progress_percentage }}%"></div>
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <a href="{{ route('orders.show', $order) }}" 
                    class="text-purple-600 hover:text-purple-800 font-medium text-sm flex items-center gap-1">
                    Lihat Detail
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @if($order->status === 'completed')
                    <a href="{{ route('orders.invoice', $order) }}" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm">
                        Lihat Invoice
                    </a>
                @endif
            </div>
        </div>
    @endforeach
</div>
