<?php

namespace App\Http\Controllers;

use App\Models\SurveyBooking;
use App\Models\PriceEstimate;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SurveyBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = SurveyBooking::with(['user', 'priceEstimate'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('survey-booking.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Get price estimate ID from query parameter
        $priceEstimateId = $request->query('estimate_id');
        $priceEstimate = null;
        
        if ($priceEstimateId) {
            $priceEstimate = PriceEstimate::find($priceEstimateId);
        }
        
        return view('survey-booking.create', compact('priceEstimate'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'price_estimate_id' => 'nullable|exists:price_estimates,id',
            'project_type' => 'required|string|max:255',
            'project_description' => 'required|string',
            'location' => 'required|string',
            'whatsapp_number' => 'required|string|min:9|max:13|regex:/^[0-9]+$/',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        // CHECK SCHEDULE AVAILABILITY
        $isSlotAvailable = $this->checkScheduleAvailability(
            $validated['preferred_date'],
            $validated['preferred_time']
        );

        if (!$isSlotAvailable) {
            return back()->withErrors([
                'preferred_time' => 'Slot waktu ini sudah terisi. Silakan pilih waktu lain.'
            ])->withInput();
        }

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        $booking = SurveyBooking::create($validated);

        // Create notification for user
        Notification::create([
            'user_id' => Auth::id(),
            'type' => 'booking',
            'survey_booking_id' => $booking->id,
            'title' => 'Booking Survei Berhasil',
            'message' => "Booking survei Anda untuk {$booking->project_type} telah berhasil dibuat. Menunggu konfirmasi admin.",
        ]);

        // Create notification for all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'booking',
                'survey_booking_id' => $booking->id,
                'title' => 'Booking Survei Baru',
                'message' => "Customer " . Auth::user()->name . " mengajukan booking survei {$booking->project_type}. Jadwal: " . $booking->preferred_date->format('d M Y') . " pukul " . \Carbon\Carbon::parse($booking->preferred_time)->format('H:i') . ". Lokasi: {$booking->location}. Segera tinjau dan konfirmasi!",
            ]);
        }

        // Send WhatsApp notification to admin
        $this->sendWhatsAppToAdmin($booking);

        return redirect()->route('survey-booking.show', $booking)
            ->with('success', 'Booking survei berhasil dibuat! Admin akan segera menghubungi Anda.');
    }

    /**
     * Check if schedule slot is available
     */
    private function checkScheduleAvailability($date, $time)
    {
        // Check if there's already a confirmed booking at the same date & time
        $existingBooking = SurveyBooking::where('preferred_date', $date)
            ->where('preferred_time', $time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        return !$existingBooking;
    }

    /**
     * Get available time slots for a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        $date = $request->query('date');
        
        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        // All available time slots
        $allSlots = ['08:00', '10:00', '13:00', '15:00'];
        
        // Get booked slots for this date
        $bookedSlots = SurveyBooking::where('preferred_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('preferred_time')
            ->map(function($time) {
                return \Carbon\Carbon::parse($time)->format('H:i');
            })
            ->toArray();
        
        // Filter out booked slots
        $availableSlots = array_diff($allSlots, $bookedSlots);
        
        return response()->json([
            'available_slots' => array_values($availableSlots),
            'booked_slots' => $bookedSlots
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SurveyBooking $booking)
    {
        // Check authorization
        if ($booking->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $booking->load(['user', 'priceEstimate', 'surveyResult', 'order']);
        
        return view('survey-booking.show', ['surveyBooking' => $booking]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SurveyBooking $booking)
    {
        // Only allow edit if status is pending
        if ($booking->status !== 'pending') {
            return redirect()->route('survey-booking.show', $booking)
                ->with('error', 'Booking yang sudah dikonfirmasi tidak dapat diedit.');
        }

        // Check authorization
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        return view('survey-booking.edit', ['surveyBooking' => $booking]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SurveyBooking $booking)
    {
        // Check authorization
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow update if status is pending
        if ($booking->status !== 'pending') {
            return redirect()->route('survey-booking.show', $booking)
                ->with('error', 'Booking yang sudah dikonfirmasi tidak dapat diubah.');
        }

        $validated = $request->validate([
            'project_type' => 'required|string|max:255',
            'project_description' => 'required|string',
            'location' => 'required|string',
            'whatsapp_number' => 'required|string|min:9|max:13|regex:/^[0-9]+$/',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        $booking->update($validated);

        return redirect()->route('survey-booking.show', $booking)
            ->with('success', 'Booking survei berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SurveyBooking $booking)
    {
        // Check authorization
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow delete if status is pending
        if ($booking->status !== 'pending') {
            return redirect()->route('survey-booking.index')
                ->with('error', 'Booking yang sudah dikonfirmasi tidak dapat dihapus.');
        }

        $booking->delete();

        return redirect()->route('survey-booking.index')
            ->with('success', 'Booking survei berhasil dihapus.');
    }

    /**
     * Send WhatsApp notification to admin (FREE via link)
     */
    protected function sendWhatsAppToAdmin($booking)
    {
        $adminPhone = '6285292674783'; // Admin phone number
        
        $message = "🔔 *BOOKING SURVEI BARU*\n\n";
        $message .= "📋 Project: {$booking->project_type}\n";
        $message .= "👤 Customer: " . $booking->user->name . "\n";
        $message .= "📍 Lokasi: {$booking->location}\n";
        $message .= "📅 Tanggal: " . $booking->preferred_date->format('d M Y') . "\n";
        $message .= "🕐 Waktu: " . \Carbon\Carbon::parse($booking->preferred_time)->format('H:i') . "\n";
        $message .= "\n📝 Deskripsi:\n{$booking->project_description}";
        
        if ($booking->notes) {
            $message .= "\n\n💬 Catatan: {$booking->notes}";
        }
        
        $message .= "\n\n🔗 Lihat detail: " . route('admin.survey-bookings.show', $booking->id);

        // URL encode the message
        $encodedMessage = urlencode($message);
        $whatsappUrl = "https://wa.me/{$adminPhone}?text={$encodedMessage}";

        // Update booking to mark WhatsApp as sent
        $booking->update([
            'whatsapp_sent' => true,
            'whatsapp_sent_at' => now(),
        ]);

        // In real implementation, you can:
        // 1. Use WhatsApp Business API (paid)
        // 2. Use third-party services like Fonnte, Wablas, etc
        // Or just log the URL for manual sending
        
        Log::info("WhatsApp notification URL: {$whatsappUrl}");
        
        return $whatsappUrl;
    }

    /**
     * Show booking history with filters
     */
    public function history(Request $request)
    {
        $query = SurveyBooking::with(['user', 'priceEstimate', 'order'])
            ->where('user_id', Auth::id());

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('preferred_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('preferred_date', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('survey-booking.history', compact('bookings'));
    }

    /**
     * Show price offer view
     */
    public function showPriceOffer(SurveyBooking $booking)
    {
        // Authorization check
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $surveyResult = $booking->surveyResult;
        $payment = $booking->payment;

        return view('survey-booking.price-offer', compact('booking', 'surveyResult', 'payment'));
    }

    /**
     * Accept price offer
     */
    public function acceptPrice(SurveyBooking $booking)
    {
        // Authorization check
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $payment = $booking->payment;

        if (!$payment) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($payment->status !== 'pending') {
            return back()->with('error', 'Penawaran sudah diproses sebelumnya.');
        }

        // Notify admin
        Notification::create([
            'user_id' => 1, // Admin
            'type' => 'price_accepted',
            'survey_booking_id' => $booking->id,
            'title' => 'Customer Menyetujui Penawaran',
            'message' => "{$booking->user->name} menyetujui penawaran harga Rp " . number_format($payment->total_price, 0, ',', '.') . " untuk {$booking->project_type}. Menunggu upload bukti pembayaran DP.",
        ]);

        return redirect()->route('payments.create', ['payment_id' => $payment->id])
            ->with('success', 'Anda menyetujui penawaran! Silakan upload bukti pembayaran DP.');
    }

    /**
     * Reject price offer
     */
    public function rejectPrice(Request $request, SurveyBooking $booking)
    {
        // Authorization check
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'reject_reason' => 'nullable|string|max:500',
        ]);

        $payment = $booking->payment;

        if (!$payment) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($payment->status !== 'pending') {
            return back()->with('error', 'Penawaran sudah diproses sebelumnya.');
        }

        // Update booking status
        $booking->update([
            'status' => 'cancelled',
            'cancel_reason' => $validated['reject_reason'] ?? 'Customer menolak penawaran harga',
        ]);

        // Update payment status
        $payment->update([
            'status' => 'rejected',
        ]);

        // Notify admin
        Notification::create([
            'user_id' => 1, // Admin
            'type' => 'price_rejected',
            'survey_booking_id' => $booking->id,
            'title' => 'Customer Menolak Penawaran',
            'message' => "{$booking->user->name} menolak penawaran harga Rp " . number_format($payment->total_price, 0, ',', '.') . " untuk {$booking->project_type}. Alasan: " . ($validated['reject_reason'] ?? 'Tidak disebutkan'),
            'data' => json_encode([
                'reject_reason' => $validated['reject_reason'] ?? null,
            ]),
        ]);

        return redirect()->route('survey-booking.index')
            ->with('success', 'Penawaran ditolak. Booking telah dibatalkan.');
    }
}
