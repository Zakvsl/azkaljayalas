<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceEstimateRequest;
use App\Http\Resources\PriceEstimateResource;
use App\Models\PriceEstimate;
use App\Services\PriceEstimationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class PriceEstimateController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    protected PriceEstimationService $estimationService;

    public function __construct(PriceEstimationService $estimationService)
    {
        $this->estimationService = $estimationService;
        $this->middleware('auth');
    }

    /**
     * Display a list of price estimates.
     */
    public function index(): View
    {
        $estimates = PriceEstimate::query()
            ->when(!Auth::user()->isAdmin(), function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return view('estimates.index', compact('estimates'));
    }

    /**
     * Show the estimation form.
     */
    public function create(): View
    {
        return view('estimates.create');
    }

    /**
     * Get instant price estimate.
     */
    public function estimate(PriceEstimateRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $estimatedPrice = $this->estimationService->predictPrice($validated);

            Log::info('Price estimate generated', [
                'user_id' => Auth::id(),
                'input' => $validated,
                'estimated_price' => $estimatedPrice
            ]);

            return response()->json([
                'success' => true,
                'estimated_price' => $estimatedPrice,
                'formatted_price' => 'Rp ' . number_format($estimatedPrice, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            Log::error('Price estimation failed', [
                'user_id' => Auth::id(),
                'input' => $request->validated(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate price estimate. Please try again later.',
            ], 500);
        }
    }

    /**
     * Store a new price estimate.
     */
    public function store(PriceEstimateRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $estimatedPrice = $this->estimationService->predictPrice($validated);

            $estimate = PriceEstimate::create([
                'user_id' => Auth::id(),
                'project_type' => $validated['project_type'],
                'material_type' => $validated['material_type'],
                'dimensions' => $validated['dimensions'],
                'additional_features' => $validated['additional_features'] ?? [],
                'estimated_price' => $estimatedPrice,
                'notes' => $validated['notes'] ?? null,
            ]);

            Log::info('Price estimate stored', [
                'user_id' => Auth::id(),
                'estimate_id' => $estimate->id,
                'estimated_price' => $estimatedPrice
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Price estimate created successfully.',
                'estimate' => $estimate,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to store price estimate', [
                'user_id' => Auth::id(),
                'input' => $request->validated(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store price estimate. Please try again later.',
            ], 500);
        }
    }

    /**
     * Display a specific price estimate.
     */
    public function show(PriceEstimate $estimate): View
    {
        $this->authorize('view', $estimate);
        return view('estimates.show', compact('estimate'));
    }

    /**
     * Update the status of a price estimate.
     */
    public function updateStatus(Request $request, PriceEstimate $estimate): JsonResponse
    {
        $this->authorize('manageStatus', $estimate);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:confirmed,rejected'],
            'actual_price' => ['required_if:status,confirmed', 'nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $estimate->update([
                'status' => $validated['status'],
                'actual_price' => $validated['actual_price'] ?? null,
                'notes' => $validated['notes'] ?? $estimate->notes,
            ]);

            Log::info('Price estimate status updated', [
                'estimate_id' => $estimate->id,
                'old_status' => $estimate->getOriginal('status'),
                'new_status' => $validated['status'],
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estimate status updated successfully.',
                'estimate' => $estimate->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update estimate status', [
                'estimate_id' => $estimate->id,
                'input' => $validated,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update estimate status. Please try again later.',
            ], 500);
        }
    }

    /**
     * Delete a price estimate.
     */
    public function destroy(PriceEstimate $estimate): RedirectResponse
    {
        $this->authorize('delete', $estimate);

        try {
            $estimate->delete();

            Log::info('Price estimate deleted', [
                'estimate_id' => $estimate->id,
                'deleted_by' => Auth::id(),
            ]);

            return redirect()
                ->route('estimates.index')
                ->with('success', 'Estimate deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete estimate', [
                'estimate_id' => $estimate->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('estimates.index')
                ->with('error', 'Failed to delete estimate. Please try again later.');
        }
    }
}