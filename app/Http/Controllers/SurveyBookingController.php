<?php

namespace App\Http\Controllers;

use App\Mail\SurveyBookingAdminNotification;
use App\Mail\SurveyBookingCustomerConfirmation;
use App\Models\Finishing;
use App\Models\Kerumitan;
use App\Models\Ketebalan;
use App\Models\Material;
use App\Models\Product;
use App\Models\SurveyBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SurveyBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $bookings = SurveyBooking::with(['product', 'material', 'finishing', 'kerumitan', 'ketebalan'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('survey-booking.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $products = Product::where('is_active', true)->get();
        $materials = Material::where('is_active', true)->get();
        $finishings = Finishing::where('is_active', true)->get();
        $kerumitans = Kerumitan::where('is_active', true)->get();
        $ketebalans = Ketebalan::where('is_active', true)->get();
        
        return view('survey-booking.create', compact('products', 'materials', 'finishings', 'kerumitans', 'ketebalans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'survey_date' => 'required|date|after:today',
            'survey_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
            'product_id' => 'nullable|integer',
            'material_id' => 'nullable|integer',
            'finishing_id' => 'nullable|integer',
            'kerumitan_id' => 'nullable|integer',
            'ketebalan_id' => 'nullable|integer',
            'width' => 'nullable|numeric|min:0.1',
            'height' => 'nullable|numeric|min:0.1',
            'length' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
        ]);
        
        // Create the booking
        $booking = SurveyBooking::create($validated);
        
        // Send confirmation email to customer
        $this->sendCustomerConfirmationEmail($booking);
        
        // Send notification email to admin
        $this->sendAdminNotificationEmail($booking);
        
        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibuat. Kami akan menghubungi Anda segera.',
            'booking_id' => $booking->id
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking = SurveyBooking::with(['product', 'material', 'finishing', 'kerumitan', 'ketebalan'])->findOrFail($id);
        return view('survey-booking.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $booking = SurveyBooking::with(['product', 'material', 'finishing', 'kerumitan', 'ketebalan'])->findOrFail($id);
        $products = Product::where('is_active', true)->get();
        $materials = Material::where('is_active', true)->get();
        $finishings = Finishing::where('is_active', true)->get();
        $kerumitans = Kerumitan::where('is_active', true)->get();
        $ketebalans = Ketebalan::where('is_active', true)->get();
        
        return view('survey-booking.edit', compact('booking', 'products', 'materials', 'finishings', 'kerumitans', 'ketebalans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $booking = SurveyBooking::findOrFail($id);
        
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'survey_date' => 'required|date',
            'survey_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
            'product_id' => 'nullable|integer',
            'material_id' => 'nullable|integer',
            'finishing_id' => 'nullable|integer',
            'kerumitan_id' => 'nullable|integer',
            'ketebalan_id' => 'nullable|integer',
            'width' => 'nullable|numeric|min:0.1',
            'height' => 'nullable|numeric|min:0.1',
            'length' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);
        
        // Update the booking
        $booking->update($validated);
        
        return redirect()->route('survey-booking.show', $booking->id)
            ->with('success', 'Booking berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $booking = SurveyBooking::findOrFail($id);
        $booking->delete();
        
        return redirect()->route('survey-booking.index')
            ->with('success', 'Booking berhasil dihapus.');
    }
    
    /**
     * Send confirmation email to customer
     */
    private function sendCustomerConfirmationEmail(SurveyBooking $booking)
    {
        try {
            Mail::to($booking->email)
                ->send(new SurveyBookingCustomerConfirmation($booking));
        } catch (\Exception $e) {
            // Log the error but don't stop execution
            \Illuminate\Support\Facades\Log::error('Failed to send customer confirmation email: ' . $e->getMessage());
        }
    }
    
    /**
     * Send notification email to admin
     */
    private function sendAdminNotificationEmail(SurveyBooking $booking)
    {
        try {
            Mail::to(config('mail.from.address'))
                ->send(new SurveyBookingAdminNotification($booking));
        } catch (\Exception $e) {
            // Log the error but don't stop execution
            \Illuminate\Support\Facades\Log::error('Failed to send admin notification email: ' . $e->getMessage());
        }
    }
}
