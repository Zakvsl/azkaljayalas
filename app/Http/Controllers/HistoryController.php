<?php

namespace App\Http\Controllers;

use App\Models\SurveyBooking;
use App\Models\Payment;
use App\Models\Order;
use App\Models\PriceEstimate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Display user history with 4 categories: Booking, Survey, Penawaran, Pesanan
     */
    public function index(Request $request)
    {
        $category = $request->get('category', 'booking'); // Default: booking

        // Stats
        $stats = [
            'booking' => SurveyBooking::where('user_id', Auth::id())
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'survey' => SurveyBooking::where('user_id', Auth::id())
                ->where('status', 'confirmed')
                ->whereHas('surveyResult')
                ->count(),
            'penawaran' => Payment::where('user_id', Auth::id())
                ->where('status', 'pending')
                ->count(),
            'pesanan' => Order::where('user_id', Auth::id())
                ->whereIn('status', ['dp_confirmed', 'in_progress', 'ready_for_pickup', 'completed'])
                ->count(),
        ];

        // Data based on category
        $data = [];
        switch ($category) {
            case 'booking':
                $data = SurveyBooking::with(['priceEstimate'])
                    ->where('user_id', Auth::id())
                    ->whereIn('status', ['pending', 'confirmed', 'cancelled'])
                    ->latest()
                    ->paginate(10);
                break;

            case 'survey':
                $data = SurveyBooking::with(['surveyResult', 'priceEstimate'])
                    ->where('user_id', Auth::id())
                    ->where('status', 'confirmed')
                    ->whereHas('surveyResult')
                    ->latest()
                    ->paginate(10);
                break;

            case 'penawaran':
                $data = Payment::with(['surveyBooking'])
                    ->where('user_id', Auth::id())
                    ->whereIn('status', ['pending', 'waiting_confirmation'])
                    ->latest()
                    ->paginate(10);
                break;

            case 'pesanan':
                $data = Order::with(['surveyBooking', 'payments'])
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->paginate(10);
                break;
        }

        return view('history.index', compact('category', 'stats', 'data'));
    }
}
