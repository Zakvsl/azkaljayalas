<?php

namespace App\Http\Controllers;

use App\Services\PredictionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PredictionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('prediction.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('prediction.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'material_id' => 'required|integer',
            'finishing_id' => 'required|integer',
            'kerumitan_id' => 'required|integer',
            'ketebalan_id' => 'required|integer',
            'width' => 'required|numeric|min:0.1',
            'height' => 'required|numeric|min:0.1',
            'length' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);
        
        // Get prediction from service
        $predictionService = new PredictionService();
        $result = $predictionService->predictPrice($validated);
        
        // Return response
        return response()->json($result);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not used in this implementation
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not used in this implementation
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Not used in this implementation
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Not used in this implementation
        abort(404);
    }
    
    /**
     * Calculate price prediction via AJAX
     */
    public function calculate(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'material_id' => 'required|integer',
            'finishing_id' => 'required|integer',
            'kerumitan_id' => 'required|integer',
            'ketebalan_id' => 'required|integer',
            'width' => 'required|numeric|min:0.1',
            'height' => 'required|numeric|min:0.1',
            'length' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);
        
        // Get prediction from service
        $predictionService = new PredictionService();
        $result = $predictionService->predictPrice($validated);
        
        // Return response
        return response()->json($result);
    }
}
