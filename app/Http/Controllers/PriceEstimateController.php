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
use Illuminate\Validation\Rule;

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
            // Determine validation rules based on product type
            $jenisProduk = $request->input('jenis_produk');
            
            $rules = [
                'jenis_produk' => ['required', 'string', 'in:Pagar,Kanopi,Railing,Teralis,Pintu Handerson,Pintu Gerbang'],
                'jumlah_unit' => ['required', 'integer', 'min:1'],
                'jenis_material' => ['required', 'string', 'in:Hollow,Hollow Stainless,Pipa Stainless'],
                'profile_size' => ['required','string',
                                    \Illuminate\Validation\Rule::in([
                                    '4x4', '4x6', '4x8',      // Hollow
                                    '1x3', '2x2',            // Teralis
                                    '1.5 inch', '2 inch',    // Stainless
                                ]),
                ],

                'ketebalan_mm' => ['required', 'numeric', 'min:0.1'],
                'finishing' => ['nullable', 'string', 'in:Tanpa Cat,Cat Dasar,Cat Biasa,Cat Duco'],
                'kerumitan_desain' => ['required', 'string', 'in:Sederhana,Menengah,Kompleks'],
            ];
            
            // Auto-set finishing untuk material Stainless
            if (in_array($request->input('jenis_material'), ['Hollow Stainless', 'Pipa Stainless'])) {
                $request->merge(['finishing' => 'Tanpa Cat']);
            }
            
            // Conditional validation based on product type
            if ($jenisProduk === 'Teralis') {
                // Teralis: per lubang
                $rules['jumlah_lubang'] = ['required', 'integer', 'min:1'];
                $rules['ukuran_m2'] = ['nullable', 'numeric', 'min:0'];
            } elseif ($jenisProduk === 'Railing') {
                // Railing: per meter (PER-M)
                $rules['jumlah_lubang'] = ['nullable', 'integer', 'min:0'];
                $rules['ukuran_m'] = ['required', 'numeric', 'min:0.1'];
                $rules['ukuran_m2'] = ['nullable', 'numeric', 'min:0'];
            } else {
                // Others: per m²
                $rules['jumlah_lubang'] = ['nullable', 'integer', 'min:0'];
                $rules['ukuran_m2'] = ['required', 'numeric', 'min:0.1'];
            }
            
            $validated = $request->validate($rules);

            $prediction = $this->estimationService->predictPrice($validated);
            $estimatedPrice = is_array($prediction) ? $prediction['price'] : $prediction;
            $predictionMethod = is_array($prediction) ? $prediction['method'] : 'unknown';
            $modelAccuracy = is_array($prediction) ? $prediction['model_accuracy'] : null;

            Log::info('Price estimate generated', [
                'input' => $validated,
                'estimated_price' => $estimatedPrice,
                'prediction_method' => $predictionMethod
            ]);

            return response()->json([
                'success' => true,
                'harga_akhir' => $estimatedPrice,
                'formatted_price' => 'Rp ' . number_format($estimatedPrice, 0, ',', '.'),
                'prediction_method' => $predictionMethod,
                'model_accuracy' => $modelAccuracy,
                'message' => $predictionMethod === 'ml' 
                    ? 'Prediksi menggunakan Machine Learning (akurasi 97.3%)' 
                    : 'Prediksi menggunakan formula standar'
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
            // Validate input - accept format dari form (Hollow, Pipa Stainless, Cat Biasa, Sederhana, dll)
            $validated = $request->validate([
                'jenis_produk' => 'required|in:Pagar,Kanopi,Railing,Teralis,Pintu Handerson,Pintu Gerbang',
                'jumlah_unit' => 'required|integer|min:1',
                'jumlah_lubang' => 'nullable|numeric|min:0',
                'ukuran_m2' => 'nullable|numeric|min:0',
                'ukuran_m' => 'nullable|numeric|min:0',
                'jenis_material' => 'required|in:Hollow,Hollow Stainless,Pipa Stainless',
                'profile_size' => 'nullable|string',
                'ketebalan_mm' => 'required|numeric|min:0',
                'finishing' => 'nullable|in:Tanpa Cat,Cat Dasar,Cat Biasa,Cat Duco',
                'kerumitan_desain' => 'required|in:Sederhana,Menengah,Kompleks',
                'harga_akhir' => 'required|numeric|min:0',
                'notes' => 'nullable|string'
            ]);
            
            // Auto-set finishing untuk material Stainless
            if (in_array($validated['jenis_material'], ['Hollow Stainless', 'Pipa Stainless']) && empty($validated['finishing'])) {
                $validated['finishing'] = 'Tanpa Cat';
            }
            
            // Convert form values to database format
            $finishingMap = [
                'Tanpa Cat' => 'cat_biasa',  // Default jika tanpa cat
                'Cat Dasar' => 'cat_biasa',
                'Cat Biasa' => 'cat_biasa',
                'Cat Duco' => 'cat_epoxy',
                'Powder Coating' => 'powder_coating',
                'Galvanis' => 'galvanis'
            ];
            
            $kerumitanMap = [
                'Sederhana' => 1,
                'Menengah' => 2,
                'Kompleks' => 3
            ];
            
            $materialMap = [
                'Hollow' => 'hollow',
                'Hollow Stainless' => 'stainless',
                'Pipa Stainless' => 'stainless'
            ];
            
            $finishing = $finishingMap[$validated['finishing'] ?? 'Tanpa Cat'] ?? 'cat_biasa';
            $kerumitan = $kerumitanMap[$validated['kerumitan_desain']] ?? 1;
            $material = $materialMap[$validated['jenis_material']] ?? 'hollow';
            
            // Determine metode_hitung based on jenis_produk
            if ($validated['jenis_produk'] === 'Teralis') {
                $metodeHitung = 'PER-LUBANG';
            } elseif ($validated['jenis_produk'] === 'Railing') {
                $metodeHitung = 'PER-M';
            } else {
                $metodeHitung = 'PER-M2';
            }
            
            // Use harga_akhir from request (already calculated by estimate endpoint)
            $estimatedPrice = $validated['harga_akhir'];

            // Store estimate
            $estimate = PriceEstimate::create([
                'user_id' => Auth::id(),
                'produk' => $validated['jenis_produk'],
                'jenis_produk' => $validated['jenis_produk'],
                'jumlah_unit' => $validated['jumlah_unit'],
                'jumlah_lubang' => $validated['jumlah_lubang'] ?? 0,
                'ukuran_m2' => $validated['ukuran_m2'] ?? 0,
                'jenis_material' => $material,
                'profile_size' => $validated['profile_size'] ?? null,
                'ketebalan_mm' => $validated['ketebalan_mm'],
                'finishing' => $finishing,
                'kerumitan_desain' => $kerumitan,
                'metode_hitung' => $metodeHitung,
                'harga_akhir' => $estimatedPrice,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            Log::info('Price estimate stored with ML prediction', [
                'user_id' => Auth::id(),
                'estimate_id' => $estimate->id,
                'predicted_price' => $estimatedPrice
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estimasi berhasil disimpan.',
                'estimate_id' => $estimate->id,
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