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
            ->when(!(bool) (Auth::user()->is_admin ?? false) && (Auth::user()->role ?? null) !== 'admin', function ($query) {
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
    public function estimate(Request $request): JsonResponse
    {
        try {
            // Validasi manual tanpa PriceEstimateRequest (karena tidak perlu harga_akhir)
            $validated = $request->validate([
                'jenis_produk' => ['required', 'string', 'in:Pagar,Kanopi,Railing,Teralis,Pintu,Tangga'],
                'jumlah_unit' => ['required', 'integer', 'min:1'],
                'jumlah_lubang' => ['required_if:jenis_produk,Teralis', 'nullable', 'integer', 'min:1'],
                'ukuran_m2' => ['required_unless:jenis_produk,Teralis', 'nullable', 'numeric', 'min:0.1'],
                'jenis_material' => ['required', 'string', 'in:hollow,besi_siku,aluminium,stainless,plat'],
                'profile_size' => ['required_unless:jenis_material,plat', 'nullable', 'string', 'max:50'],
                'ketebalan_mm' => ['required', 'numeric', 'min:0.1'],
                'finishing' => ['required', 'string', 'in:cat_biasa,cat_epoxy,powder_coating,galvanis'],
                'kerumitan_desain' => ['required', 'integer', 'in:1,2,3'],
            ]);

            $estimatedPrice = $this->estimationService->predictPrice($validated);

            Log::info('Price estimate generated', [
                'input' => $validated,
                'estimated_price' => $estimatedPrice
            ]);

            return response()->json([
                'success' => true,
                'harga_akhir' => $estimatedPrice,
                'formatted_price' => 'Rp ' . number_format($estimatedPrice, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            Log::error('Price estimation failed', [
                'input' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung estimasi: ' . $e->getMessage(),
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

            $estimate = PriceEstimate::create([
                'user_id' => Auth::id(),
                'jenis_produk' => $validated['jenis_produk'],
                'jumlah_unit' => $validated['jumlah_unit'],
                'jumlah_lubang' => $validated['jumlah_lubang'] ?? null,
                'ukuran_m2' => $validated['ukuran_m2'] ?? null,
                'jenis_material' => $validated['jenis_material'],
                'profile_size' => $validated['profile_size'] ?? null,
                'ketebalan_mm' => $validated['ketebalan_mm'],
                'finishing' => $validated['finishing'],
                'kerumitan_desain' => $validated['kerumitan_desain'],
                'harga_akhir' => $validated['harga_akhir'],
                'notes' => $validated['notes'] ?? null,
            ]);

            Log::info('Price estimate stored', [
                'user_id' => Auth::id(),
                'estimate_id' => $estimate->id,
                'harga_akhir' => $validated['harga_akhir']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estimasi berhasil disimpan.',
                'estimate' => $estimate,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to store price estimate', [
                'user_id' => Auth::id(),
                'input' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan estimasi: ' . $e->getMessage(),
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