<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as BaseController;
use App\Services\MLPredictionService;
use Illuminate\Http\Request;
use Exception;

class MLModelController extends BaseController
{
    protected $mlService;

    public function __construct(MLPredictionService $mlService)
    {
        $this->middleware(['auth', 'admin']);
        $this->mlService = $mlService;
    }

    /**
     * Show ML model management page
     */
    public function index()
    {
        $modelInfo = $this->mlService->getModelInfo();
        return view('admin.ml.index', compact('modelInfo'));
    }

    /**
     * Train the ML model
     */
    public function train(Request $request)
    {
        try {
            $datasetPath = base_path('dataset_transaksi_bengkel_las_130.xlsx');

            if (!file_exists($datasetPath)) {
                return back()->with('error', 'Dataset file tidak ditemukan!');
            }

            $results = $this->mlService->trainModel($datasetPath);

            return back()->with('success', 'Model berhasil ditraining!')
                ->with('training_results', $results);

        } catch (Exception $e) {
            return back()->with('error', 'Training gagal: ' . $e->getMessage());
        }
    }

    /**
     * Test prediction
     */
    public function testPrediction(Request $request)
    {
        $validated = $request->validate([
            'jenis_proyek' => 'required|string',
            'panjang' => 'required|numeric|min:0',
            'lebar' => 'required|numeric|min:0',
            'tinggi' => 'required|numeric|min:0',
            'material' => 'required|string',
            'kompleksitas' => 'required|string',
        ]);

        try {
            $prediction = $this->mlService->predictPrice($validated);

            return response()->json([
                'success' => true,
                'predicted_price' => $prediction,
                'formatted_price' => 'Rp ' . number_format($prediction, 0, ',', '.')
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
