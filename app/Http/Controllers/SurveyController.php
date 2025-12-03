<?php

namespace App\Http\Controllers;

use App\Models\SurveyBooking;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class SurveyController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan form survey
    public function create(Request $request)
    {
        $priceEstimate = null;
        if ($request->has('estimate_id')) {
            $priceEstimate = \App\Models\PriceEstimate::find($request->estimate_id);
            \Log::info('Survey Create - Estimate ID: ' . $request->estimate_id);
            \Log::info('Survey Create - Price Estimate Found: ' . ($priceEstimate ? 'Yes' : 'No'));
            if ($priceEstimate) {
                \Log::info('Survey Create - Estimate Data: ' . json_encode($priceEstimate->toArray()));
            }
        } else {
            \Log::info('Survey Create - No estimate_id in request');
        }
        return view('survey.create', compact('priceEstimate'));
    }

    // Menyimpan data survey
    public function store(Request $request)
    {
        $validated = $request->validate([
            'price_estimate_id' => 'nullable|exists:price_estimates,id',
            'project_type' => 'required|string',
            'project_description' => 'required|string',
            'location' => 'required|string',
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'notes' => 'nullable|string'
        ]);
        
        $survey = SurveyBooking::create([
            'user_id' => $request->user()->id,
            'price_estimate_id' => $validated['price_estimate_id'] ?? null,
            'project_type' => $validated['project_type'],
            'project_description' => $validated['project_description'],
            'location' => $validated['location'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'preferred_date' => $validated['preferred_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending'
        ]);

        // Create notification for user who created the booking
        Notification::create([
            'user_id' => $request->user()->id,
            'type' => 'booking',
            'survey_booking_id' => $survey->id,
            'title' => 'Booking Survei Berhasil Dibuat',
            'message' => "Booking survei untuk proyek {$survey->project_type} telah dibuat. Menunggu konfirmasi admin.",
        ]);

        // Create notification for admins only if user is NOT an admin
        if ($request->user()->role !== 'admin') {
            $admins = User::where('role', 'admin')->get();
            
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'booking',
                    'survey_booking_id' => $survey->id,
                    'title' => 'Booking Survei Baru',
                    'message' => "Customer {$request->user()->name} mengajukan booking survei {$survey->project_type}. Jadwal: {$survey->preferred_date->format('d M Y')}. Lokasi: {$survey->location}. Segera tinjau dan konfirmasi!",
                ]);
            }
        }

        return redirect()->route('home')->with('success', 'Permintaan survei berhasil dikirim!');
    }
}