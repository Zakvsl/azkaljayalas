<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as BaseController;
use App\Models\SurveyBooking;
use Illuminate\Http\Request;

class SurveyBookingController extends BaseController
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of survey bookings
     */
    public function index(Request $request)
    {
        $query = SurveyBooking::with('user')->latest();

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by user name or location
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('location', 'like', "%{$search}%")
                  ->orWhere('project_type', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $surveys = $query->paginate(10)->withQueryString();

        return view('admin.survey-bookings.index', compact('surveys'));
    }

    /**
     * Display the specified survey booking
     */
    public function show(SurveyBooking $surveyBooking)
    {
        $surveyBooking->load('user', 'priceEstimate');
        return view('admin.survey-bookings.show', compact('surveyBooking'));
    }

    /**
     * Update the status of the survey booking
     */
    public function updateStatus(Request $request, SurveyBooking $surveyBooking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,completed,cancelled'
        ]);

        $surveyBooking->update([
            'status' => $validated['status']
        ]);

        return back()->with('success', 'Status survei berhasil diperbarui!');
    }

    /**
     * Remove the specified survey booking
     */
    public function destroy(SurveyBooking $surveyBooking)
    {
        $surveyBooking->delete();
        return redirect()->route('admin.survey-bookings.index')
            ->with('success', 'Data booking survei berhasil dihapus!');
    }
}
