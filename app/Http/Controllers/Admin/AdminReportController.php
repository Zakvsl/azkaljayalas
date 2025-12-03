<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyReport;
use App\Models\SurveyBooking;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    /**
     * Display monthly reports
     */
    public function index(Request $request)
    {
        $query = MonthlyReport::query();

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        if ($request->has('month') && $request->month !== '') {
            $query->where('month', $request->month);
        }

        $reports = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(12);

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Show detailed monthly report
     */
    public function show(MonthlyReport $report)
    {
        // Get detailed data for the month
        $bookings = SurveyBooking::whereYear('created_at', $report->year)
            ->whereMonth('created_at', $report->month)
            ->with(['user', 'surveyResult'])
            ->get();

        $payments = Payment::whereYear('created_at', $report->year)
            ->whereMonth('created_at', $report->month)
            ->with(['user', 'surveyBooking'])
            ->get();

        $orders = Order::whereYear('created_at', $report->year)
            ->whereMonth('created_at', $report->month)
            ->with(['user'])
            ->get();

        return view('admin.reports.show', compact('report', 'bookings', 'payments', 'orders'));
    }

    /**
     * Generate new monthly report
     */
    public function generate(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('n'));

        // Check if report already exists
        $existingReport = MonthlyReport::where('year', $year)
            ->where('month', $month)
            ->first();

        if ($existingReport) {
            // Update existing report
            $this->updateReport($existingReport, $year, $month);
            return redirect()->route('admin.reports.index')
                ->with('success', 'Laporan berhasil diperbarui!');
        }

        // Create new report
        $report = $this->createReport($year, $month);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Laporan bulanan berhasil dibuat!');
    }

    /**
     * Auto-generate report for current month
     */
    public function autoGenerate()
    {
        $year = date('Y');
        $month = date('n');

        $report = MonthlyReport::firstOrCreate(
            ['year' => $year, 'month' => $month],
            $this->calculateReportData($year, $month)
        );

        return $report;
    }

    /**
     * Create new monthly report
     */
    protected function createReport($year, $month)
    {
        $data = $this->calculateReportData($year, $month);
        
        return MonthlyReport::create($data);
    }

    /**
     * Update existing monthly report
     */
    protected function updateReport($report, $year, $month)
    {
        $data = $this->calculateReportData($year, $month);
        $report->update($data);
        
        return $report;
    }

    /**
     * Calculate report data for a specific month
     */
    protected function calculateReportData($year, $month)
    {
        // Get all bookings for the month
        $bookings = SurveyBooking::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        $totalBookings = $bookings->count();
        $confirmedBookings = $bookings->where('status', 'confirmed')->count();
        $cancelledBookings = $bookings->where('status', 'cancelled')->count();

        // Get completed orders
        $completedOrders = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', 'completed')
            ->count();

        // Calculate revenue
        $payments = Payment::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', 'confirmed')
            ->get();

        $totalRevenue = $payments->sum('total_price');
        $totalDpCollected = $payments->sum('dp_amount');
        $totalFullPayment = $payments->sum('remaining_payment_amount');

        // Get popular products and materials
        $surveyResults = DB::table('survey_results')
            ->join('survey_bookings', 'survey_results.survey_booking_id', '=', 'survey_bookings.id')
            ->whereYear('survey_bookings.created_at', $year)
            ->whereMonth('survey_bookings.created_at', $month)
            ->select('product_id', 'material_id')
            ->get();

        $popularProducts = $surveyResults->pluck('product_id')
            ->filter()
            ->groupBy(function($item) { return $item; })
            ->map(function($group) { return $group->count(); })
            ->sortDesc()
            ->take(3)
            ->keys()
            ->toJson();

        $popularMaterials = $surveyResults->pluck('material_id')
            ->filter()
            ->groupBy(function($item) { return $item; })
            ->map(function($group) { return $group->count(); })
            ->sortDesc()
            ->take(3)
            ->keys()
            ->toJson();

        return [
            'year' => $year,
            'month' => $month,
            'total_bookings' => $totalBookings,
            'confirmed_bookings' => $confirmedBookings,
            'cancelled_bookings' => $cancelledBookings,
            'completed_orders' => $completedOrders,
            'total_revenue' => $totalRevenue,
            'total_dp_collected' => $totalDpCollected,
            'total_full_payment' => $totalFullPayment,
            'popular_products' => $popularProducts,
            'popular_materials' => $popularMaterials,
            'generated_by' => Auth::id(),
            'generated_at' => now(),
        ];
    }
}
