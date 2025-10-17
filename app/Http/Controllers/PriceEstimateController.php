<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceEstimateRequest;
use App\Http\Resources\PriceEstimateResource;
use App\Models\PriceEstimate;
use App\Services\PriceEstimationService;
use App\Services\MLPredictionService;
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
    protected MLPredictionService $mlService;

    public function __construct(
        PriceEstimationService $estimationService,
        MLPredictionService $mlService
    ) {
        $this->estimationService = $estimationService;
        $this->mlService = $mlService;
        // Only require auth for index, store, show, update methods
        $this->middleware('auth')->except(['create', 'estimate']);
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
                'jumlah_lubang' => ['required_if:jenis_produk,Teralis', 'nullable', 'integer', 'min:0'],
                'ukuran_m2' => ['required_unless:jenis_produk,Teralis', 'nullable', 'numeric', 'min:0.1'],
                'jenis_material' => ['required', 'string', 'in:hollow,besi_siku,aluminium,stainless,plat'],
                'profile_size' => ['nullable', 'string', 'max:50'],
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
     * Store a new price estimate with ML prediction.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate input - accept lowercase format from form
            $validated = $request->validate([
                'jenis_produk' => 'required|in:Pagar,Kanopi,Railing,Teralis,Pintu,Tangga',
                'produk' => 'nullable|in:Pagar,Kanopi,Railing,Teralis,Pintu,Tangga', // Alias
                'jumlah_unit' => 'required|integer|min:1',
                'jumlah_lubang' => 'nullable|numeric|min:0',
                'ukuran_m2' => 'nullable|numeric|min:0',
                'jenis_material' => 'required|in:hollow,besi_siku,aluminium,stainless,plat',
                'profile_size' => 'nullable|string',
                'ketebalan_mm' => 'required|numeric|min:0',
                'finishing' => 'required|in:cat_biasa,cat_epoxy,powder_coating,galvanis',
                'kerumitan_desain' => 'required|integer|in:1,2,3',
                'metode_hitung' => 'nullable|in:Per m²,Per Lubang',
                'harga_akhir' => 'required|numeric|min:0',
                'notes' => 'nullable|string'
            ]);
            
            // Use produk field if jenis_produk not provided (backward compat)
            $produk = $validated['jenis_produk'] ?? $validated['produk'] ?? null;
            
            // Map form data to ML format for storage
            $mlData = $this->estimationService->mapFormDataToMLFormat($validated);
            $metodeHitung = $validated['metode_hitung'] ?? $mlData['metode_hitung'];
            
            // Use harga_akhir from request (already calculated by estimate endpoint)
            $estimatedPrice = $validated['harga_akhir'];

            // Store estimate using ML format
            $estimate = PriceEstimate::create([
                'user_id' => Auth::id(),
                'produk' => $produk,
                'jenis_produk' => $produk, // Legacy field
                'jumlah_unit' => $mlData['jumlah_unit'],
                'jumlah_lubang' => $mlData['jumlah_lubang'],
                'ukuran_m2' => $mlData['ukuran_m2'],
                'jenis_material' => $mlData['jenis_material'], // ML format: Hollow, Besi, etc
                'profile_size' => $validated['profile_size'] ?? null,
                'ketebalan_mm' => $mlData['ketebalan_mm'],
                'finishing' => $mlData['finishing'], // ML format: Cat, Powder Coating, etc
                'kerumitan_desain' => $mlData['kerumitan_desain'], // ML format: Sederhana, Menengah, etc
                'metode_hitung' => $metodeHitung,
                'harga_akhir' => $estimatedPrice,
                'estimated_price' => $estimatedPrice,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            Log::info('Price estimate stored with ML prediction', [
                'user_id' => Auth::id(),
                'estimate_id' => $estimate->id(),
                'predicted_price' => $estimatedPrice
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estimasi berhasil disimpan.',
                'estimate' => $estimate,
                'predicted_price' => number_format($estimatedPrice, 0, ',', '.'),
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
     * Fallback price calculation if ML prediction fails
     */
    private function calculateFallbackPrice(array $data): float
    {
        $basePrice = 150000; // Base price per m² or per lubang
        
        // Material multiplier
        $materialMultiplier = [
            'Hollow' => 1.0,
            'Besi' => 1.2,
            'Stainless' => 1.8,
        ];
        
        // Finishing multiplier
        $finishingMultiplier = [
            'Tanpa Finishing' => 1.0,
            'Cat' => 1.15,
            'Powder Coating' => 1.3,
        ];
        
        // Complexity multiplier
        $complexityMultiplier = [
            'Sederhana' => 1.0,
            'Menengah' => 1.25,
            'Kompleks' => 1.5,
        ];
        
        $material = $data['jenis_material'] ?? 'Hollow';
        $finishing = $data['finishing'] ?? 'Tanpa Finishing';
        $complexity = $data['kerumitan_desain'] ?? 'Sederhana';
        
        // Calculate size factor
        $sizeFactor = ($data['metode_hitung'] === 'Per m²') 
            ? ($data['ukuran_m2'] ?? 1) 
            : ($data['jumlah_lubang'] ?? 1);
        
        $price = $basePrice * $sizeFactor
            * ($materialMultiplier[$material] ?? 1)
            * ($finishingMultiplier[$finishing] ?? 1)
            * ($complexityMultiplier[$complexity] ?? 1)
            * ($data['jumlah_unit'] ?? 1);
        
        return round($price, 2);
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