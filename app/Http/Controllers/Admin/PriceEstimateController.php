<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceEstimate;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PriceEstimateController extends Controller
{
    /**
     * Display a list of all price estimates (admin view).
     */
    public function index(): View
    {
        $estimates = PriceEstimate::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.estimates.index', compact('estimates'));
    }

    /**
     * Display a specific price estimate (admin view).
     */
    public function show(PriceEstimate $estimate): View
    {
        $estimate->load('user');
        return view('admin.estimates.show', compact('estimate'));
    }

    /**
     * Update the status and actual price of a price estimate.
     */
    public function update(Request $request, PriceEstimate $estimate): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,confirmed,rejected,completed'],
            'actual_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $estimate->update([
                'status' => $validated['status'],
                'actual_price' => $validated['actual_price'] ?? $estimate->actual_price,
                'notes' => $validated['notes'] ?? $estimate->notes,
            ]);

            Log::info('Admin updated price estimate', [
                'estimate_id' => $estimate->id,
                'admin_id' => Auth::id(),
                'changes' => $validated,
            ]);

            return redirect()
                ->route('admin.estimates.show', $estimate)
                ->with('success', 'Estimate updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update estimate', [
                'estimate_id' => $estimate->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to update estimate. Please try again.');
        }
    }

    /**
     * Delete a price estimate.
     */
    public function destroy(PriceEstimate $estimate): RedirectResponse
    {
        try {
            $estimateId = $estimate->id;
            $estimate->delete();

            Log::info('Admin deleted price estimate', [
                'estimate_id' => $estimateId,
                'admin_id' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.estimates.index')
                ->with('success', 'Estimate deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to delete estimate', [
                'estimate_id' => $estimate->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to delete estimate. Please try again.');
        }
    }
}
