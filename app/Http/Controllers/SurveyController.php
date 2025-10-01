<?php

namespace App\Http\Controllers;

use App\Models\SurveyBooking;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan form survey
    public function create()
    {
        return view('survey.create');
    }

    // Menyimpan data survey
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_type' => 'required|string',
            'project_description' => 'required|string',
            'location' => 'required|string',
            'preferred_date' => 'required|date|after:today',
            'notes' => 'nullable|string'
        ]);

        $survey = SurveyBooking::create([
            'user_id' => auth()->id(),
            'project_type' => $validated['project_type'],
            'project_description' => $validated['project_description'],
            'location' => $validated['location'],
            'preferred_date' => $validated['preferred_date'],
            'notes' => $validated['notes'],
            'status' => 'pending'
        ]);

        return redirect()->route('home')->with('success', 'Permintaan survei berhasil dikirim!');
    }
}