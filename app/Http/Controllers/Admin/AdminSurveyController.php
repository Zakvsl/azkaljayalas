<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyBooking;
use App\Models\SurveyResult;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Product;
use App\Models\Material;
use App\Models\Finishing;
use App\Models\Kerumitan;
use App\Models\Ketebalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminSurveyController extends Controller
{
    /**
     * Admin dashboard - all bookings
     */
    public function index()
    {
        $pendingBookings = SurveyBooking::with(['user', 'priceEstimate'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $confirmedBookings = SurveyBooking::with(['user'])
            ->where('status', 'confirmed')
            ->whereDoesntHave('surveyResult')
            ->orderBy('preferred_date', 'asc')
            ->take(5)
            ->get();

        $stats = [
            'pending' => SurveyBooking::where('status', 'pending')->count(),
            'confirmed' => SurveyBooking::where('status', 'confirmed')->count(),
            'completed' => SurveyBooking::where('status', 'completed')->count(),
            'cancelled' => SurveyBooking::where('status', 'cancelled')->count(),
        ];

        return view('admin.survey-bookings.index', compact('pendingBookings', 'confirmedBookings', 'stats'));
    }

    /**
     * Show booking detail
     */
    public function show(SurveyBooking $booking)
    {
        $booking->load(['user', 'priceEstimate', 'surveyResult', 'order']);
        return view('admin.survey-bookings.show', compact('booking'));
    }

    /**
     * Confirm booking
     */
    public function confirm(Request $request, SurveyBooking $booking)
    {
        $booking->update([
            'status' => 'confirmed',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        // Create notification for user
        Notification::create([
            'user_id' => $booking->user_id,
            'type' => 'booking',
            'survey_booking_id' => $booking->id,
            'title' => 'Booking Survei Dikonfirmasi',
            'message' => "Booking survei Anda untuk {$booking->project_type} telah dikonfirmasi. Admin akan melakukan survei pada " . $booking->preferred_date->format('d M Y') . " pukul " . \Carbon\Carbon::parse($booking->preferred_time)->format('H:i'),
        ]);

        return redirect()->back()->with('success', 'Booking berhasil dikonfirmasi.');
    }

    /**
     * Cancel booking
     */
    public function cancel(Request $request, SurveyBooking $booking)
    {
        $validated = $request->validate([
            'cancel_reason' => 'required|string|min:10',
        ], [
            'cancel_reason.required' => 'Alasan pembatalan harus diisi',
            'cancel_reason.min' => 'Alasan pembatalan minimal 10 karakter',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancel_reason' => $validated['cancel_reason'],
        ]);

        // Create notification for user
        Notification::create([
            'user_id' => $booking->user_id,
            'type' => 'booking',
            'survey_booking_id' => $booking->id,
            'title' => 'Booking Survei Dibatalkan',
            'message' => "Booking survei Anda untuk {$booking->project_type} dibatalkan. Alasan: {$validated['cancel_reason']}",
        ]);

        return redirect()->route('admin.survey-bookings.show', $booking)->with('success', 'Booking berhasil dibatalkan dan notifikasi telah dikirim ke customer.');
    }

    /**
     * Show form untuk input hasil survei
     */
    public function surveyForm(SurveyBooking $booking)
    {
        if ($booking->status !== 'confirmed') {
            return redirect()->back()->with('error', 'Booking belum dikonfirmasi.');
        }

        $products = Product::where('is_active', true)->get();
        $materials = Material::where('is_active', true)->get();
        $finishings = Finishing::where('is_active', true)->get();
        $kerumitans = Kerumitan::where('is_active', true)->get();
        $ketebalans = Ketebalan::where('is_active', true)->get();

        return view('admin.survey-bookings.form', compact('booking', 'products', 'materials', 'finishings', 'kerumitans', 'ketebalans'));
    }

    /**
     * Store hasil survei & call ML prediction
     */
    public function storeSurveyResult(Request $request, SurveyBooking $booking)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'material_id' => 'required|exists:materials,id',
            'finishing_id' => 'required|exists:finishings,id',
            'kerumitan_id' => 'required|exists:kerumitans,id',
            'ketebalan_id' => 'required|exists:ketebalans,id',
            'width' => 'required|numeric|min:0.1',
            'height' => 'required|numeric|min:0.1',
            'length' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'action' => 'required|in:save,predict',
        ]);

        $mlPrediction = null;
        $aiEstimatedPrice = 0;

        // Call ML if action is predict
        if ($validated['action'] === 'predict') {
            try {
                $mlPrediction = $this->callMLPrediction($validated);
                $aiEstimatedPrice = $mlPrediction['price'] ?? 0;
            } catch (\Exception $e) {
                Log::error('ML Prediction failed: ' . $e->getMessage());
                return back()->with('error', 'Gagal mendapatkan estimasi dari AI. Error: ' . $e->getMessage());
            }
        }

        // Create or update survey result
        $surveyResult = SurveyResult::updateOrCreate(
            ['survey_booking_id' => $booking->id],
            [
                'surveyed_by' => Auth::id(),
                'product_id' => $validated['product_id'],
                'material_id' => $validated['material_id'],
                'finishing_id' => $validated['finishing_id'],
                'kerumitan_id' => $validated['kerumitan_id'],
                'ketebalan_id' => $validated['ketebalan_id'],
                'width' => $validated['width'],
                'height' => $validated['height'],
                'length' => $validated['length'] ?? 0,
                'quantity' => $validated['quantity'],
                'ai_estimated_price' => $aiEstimatedPrice,
                'survey_notes' => $validated['notes'],
                'surveyed_at' => now(),
            ]
        );

        if ($validated['action'] === 'predict') {
            return redirect()->route('admin.survey-bookings.adjust-price', $booking)
                ->with('success', 'Hasil survei disimpan & estimasi AI berhasil! Silakan koreksi harga jika diperlukan.');
        }

        return redirect()->route('admin.survey-bookings.show', $booking)
            ->with('success', 'Hasil survei berhasil disimpan.');
    }

    /**
     * Show form untuk adjust harga ML
     */
    public function adjustPriceForm(SurveyBooking $booking)
    {
        $surveyResult = $booking->surveyResult;
        
        if (!$surveyResult) {
            return redirect()->route('admin.survey-bookings.form', $booking)
                ->with('error', 'Hasil survei belum dibuat.');
        }

        return view('admin.survey-bookings.adjust-price', compact('booking', 'surveyResult'));
    }

    /**
     * Update harga dan kirim penawaran ke user
     */
    public function sendPriceOffer(Request $request, SurveyBooking $booking)
    {
        $validated = $request->validate([
            'admin_adjusted_price' => 'required|numeric|min:0',
            'dp_percentage' => 'required|numeric|min:10|max:100',
        ]);

        $surveyResult = $booking->surveyResult;
        
        // Update harga final
        $finalPrice = $validated['admin_adjusted_price'];
        $surveyResult->update([
            'admin_adjusted_price' => $finalPrice,
            'final_price' => $finalPrice,
        ]);

        // Calculate DP amount
        $dpAmount = ($finalPrice * $validated['dp_percentage']) / 100;

        // Create payment record
        $payment = Payment::create([
            'survey_booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'payment_type' => 'dp',
            'total_price' => $finalPrice,
            'dp_percentage' => $validated['dp_percentage'],
            'dp_amount' => $dpAmount,
            'remaining_amount' => $finalPrice - $dpAmount,
            'status' => 'pending',
        ]);

        // Update booking status
        $booking->update(['status' => 'completed']);

        // Create notification for user
        Notification::create([
            'user_id' => $booking->user_id,
            'type' => 'price_offer',
            'survey_booking_id' => $booking->id,
            'title' => 'Penawaran Harga Tersedia',
            'message' => "Penawaran harga untuk {$booking->project_type} sudah tersedia. Total: Rp " . number_format($finalPrice, 0, ',', '.') . ". DP yang harus dibayar: Rp " . number_format($dpAmount, 0, ',', '.'),
            'data' => json_encode([
                'final_price' => $finalPrice,
                'dp_amount' => $dpAmount,
                'payment_id' => $payment->id,
            ]),
        ]);

        return redirect()->route('admin.survey-bookings.index')
            ->with('success', 'Penawaran harga berhasil dikirim ke customer.');
    }

    /**
     * Call ML prediction service
     */
    protected function callMLPrediction($data)
    {
        try {
            // Call Python ML service
            $response = Http::timeout(10)->post('http://localhost:5000/predict', [
                'product_id' => $data['product_id'],
                'material_id' => $data['material_id'],
                'finishing_id' => $data['finishing_id'],
                'kerumitan_id' => $data['kerumitan_id'],
                'ketebalan_id' => $data['ketebalan_id'],
                'width' => $data['width'],
                'height' => $data['height'],
                'length' => $data['length'] ?? 0,
                'quantity' => $data['quantity'],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('ML prediction failed', ['response' => $response->body()]);
            return ['price' => 0, 'error' => 'ML service unavailable'];
            
        } catch (\Exception $e) {
            Log::error('ML prediction error', ['error' => $e->getMessage()]);
            return ['price' => 0, 'error' => $e->getMessage()];
        }
    }

    /**
     * List all bookings dengan filter
     */
    public function allBookings(Request $request)
    {
        $query = SurveyBooking::with(['user', 'surveyResult']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('project_type', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.survey-bookings.all', compact('bookings'));
    }

    /**
     * Update booking status from dropdown
     */
    public function updateStatus(Request $request, SurveyBooking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled'
        ]);

        $booking->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Status booking berhasil diubah.');
    }

    /**
     * Delete booking
     */
    public function destroy(SurveyBooking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.survey-bookings.index')
            ->with('success', 'Booking berhasil dihapus.');
    }
}

