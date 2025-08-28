<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\MaintenanceRequest;
use App\Models\Complaint;
use App\Models\Owner;
use App\Models\UtilityBill;
use App\Models\PaymentVoucher;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
        $this->middleware('role:admin,manager');
    }
    /**
     * Display the main analytics dashboard
     */
    public function index(Request $request): View
    {
        $period = $request->get('period', 'month');
        $filters = $request->only(['apartment_type', 'building']);
        
        try {
            // Get basic analytics for initial page load
            $basicAnalytics = [
                'financial_overview' => $this->analyticsService->getFinancialOverview(),
                'property_performance' => $this->analyticsService->getPropertyPerformance(),
                'owner_analytics' => $this->analyticsService->getOwnerAnalytics(),
                'maintenance_analytics' => $this->analyticsService->getMaintenanceAnalytics(),
                'monthly_trends' => $this->analyticsService->getMonthlyTrends(),
            ];

            return view('analytics.dashboard', [
                'analytics' => $basicAnalytics,
                'period' => $period,
                'filters' => $filters,
                'recent_period' => now()->format('F Y'),
            ]);
        } catch (\Exception $e) {
            Log::error('Analytics Dashboard Error: ' . $e->getMessage());
            
            return view('analytics.dashboard')->with('error', 
                'Unable to load analytics data. Please try again later.'
            );
        }
    }

    /**
     * Legacy dashboard method for backward compatibility
     */
    public function dashboard()
    {
        return $this->index(request());
    }

    /**
     * Get advanced analytics data via AJAX
     */
    public function getAdvancedAnalytics(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'in:week,month,quarter,year,ytd',
            'apartment_type' => 'nullable|string',
            'building' => 'nullable|string',
        ]);

        $period = $request->get('period', 'month');
        $filters = $request->only(['apartment_type', 'building']);

        try {
            $analytics = $this->analyticsService->getAdvancedDashboardAnalytics($period, $filters);
            
            return response()->json([
                'success' => true,
                'data' => $analytics,
                'generated_at' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Advanced Analytics Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate analytics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Financial deep dive page
     */
    public function financial(Request $request): View
    {
        $period = $request->get('period', 'month');
        $filters = $request->only(['apartment_type', 'floor', 'building']);
        
        $financialData = $this->analyticsService->getFinancialOverview();
        
        return view('analytics.financial', [
            'financial_data' => $financialData,
            'period' => $period,
            'filters' => $filters,
        ]);
    }

    /**
     * Occupancy analytics page
     */
    public function occupancy(Request $request): View
    {
        $period = $request->get('period', 'month');
        $filters = $request->only(['apartment_type', 'floor', 'building']);
        
        $occupancyData = $this->analyticsService->getPropertyPerformance();
        
        return view('analytics.occupancy', [
            'occupancy_data' => $occupancyData,
            'period' => $period,
            'filters' => $filters,
        ]);
    }

    /**
     * Owner analytics page
     */
    public function owners(Request $request): View
    {
        $period = $request->get('period', 'month');
        $filters = $request->only(['apartment_type', 'floor', 'building']);
        
        $ownerData = $this->analyticsService->getOwnerAnalytics();
        
        return view('analytics.owners', [
            'owner_data' => $ownerData,
            'period' => $period,
            'filters' => $filters,
        ]);
    }

    /**
     * Maintenance analytics page
     */
    public function maintenance(Request $request): View
    {
        $period = $request->get('period', 'month');
        $filters = $request->only(['apartment_type', 'floor', 'building']);
        
        $maintenanceData = $this->analyticsService->getMaintenanceAnalytics();
        
        return view('analytics.maintenance', [
            'maintenance_data' => $maintenanceData,
            'period' => $period,
            'filters' => $filters,
        ]);
    }

    /**
     * Generate comprehensive report
     */
    public function generateReport(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'required|in:week,month,quarter,year,ytd',
            'format' => 'in:pdf,excel,json',
            'sections' => 'array',
            'sections.*' => 'in:financial,occupancy,maintenance,owners,predictive',
        ]);

        $period = $request->get('period');
        $format = $request->get('format', 'json');
        $sections = $request->get('sections', ['financial', 'occupancy', 'maintenance', 'owners']);
        $filters = $request->only(['apartment_type', 'floor', 'building']);

        try {
            $report = $this->analyticsService->getAdvancedDashboardAnalytics($period, $filters);
            
            // Filter sections if specified
            if (!empty($sections)) {
                $filteredReport = array_intersect_key($report, array_flip($sections));
                $filteredReport['metadata'] = $report['metadata'] ?? [];
                $report = $filteredReport;
            }

            return response()->json([
                'success' => true,
                'report' => $report,
                'generated_at' => now()->toISOString(),
                'period' => $period,
                'filters' => $filters,
            ]);
        } catch (\Exception $e) {
            Log::error('Report Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get real-time metrics for dashboard widgets
     */
    public function getRealTimeMetrics(): JsonResponse
    {
        try {
            $metrics = [
                'current_occupancy' => $this->analyticsService->getPropertyPerformance()['occupancy_rate'] ?? 0,
                'today_payments' => $this->getTodaysPayments(),
                'pending_maintenance' => $this->getPendingMaintenance(),
                'overdue_invoices' => $this->getOverdueInvoices(),
                'monthly_revenue' => $this->getMonthlyRevenue(),
                'last_updated' => now()->toISOString(),
            ];

            return response()->json([
                'success' => true,
                'metrics' => $metrics,
            ]);
        } catch (\Exception $e) {
            Log::error('Real-time Metrics Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch real-time metrics',
            ], 500);
        }
    }

    /**
     * Get data for specific chart/visualization
     */
    public function getChartData(Request $request): JsonResponse
    {
        $request->validate([
            'chart_type' => 'required|in:revenue_trend,occupancy_trend,payment_methods,maintenance_categories,owner_satisfaction',
            'period' => 'in:week,month,quarter,year',
        ]);

        $chartType = $request->get('chart_type');
        $period = $request->get('period', 'month');
        $filters = $request->only(['apartment_type', 'floor', 'building']);

        try {
            $data = $this->getSpecificChartData($chartType, $period, $filters);
            
            return response()->json([
                'success' => true,
                'chart_data' => $data,
                'chart_type' => $chartType,
                'period' => $period,
            ]);
        } catch (\Exception $e) {
            Log::error('Chart Data Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate chart data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,xlsx,pdf',
            'period' => 'in:week,month,quarter,year,ytd',
            'data_type' => 'in:financial,occupancy,maintenance,owners,comprehensive',
        ]);

        $format = $request->get('format');
        $period = $request->get('period', 'month');
        $dataType = $request->get('data_type', 'comprehensive');
        $filters = $request->only(['apartment_type', 'floor', 'building']);

        try {
            $data = $this->getExportData($dataType, $period, $filters);
            $filename = "altezza_analytics_{$dataType}_{$period}_" . now()->format('Y-m-d');

            switch ($format) {
                case 'csv':
                    return $this->exportToCSV($data, $filename);
                case 'xlsx':
                    return response()->json(['message' => 'Excel export feature coming soon']);
                case 'pdf':
                    return response()->json(['message' => 'PDF export feature coming soon']);
            }
        } catch (\Exception $e) {
            Log::error('Export Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    /**
     * Clear analytics cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            Cache::tags(['analytics'])->flush();
            
            return response()->json([
                'success' => true,
                'message' => 'Analytics cache cleared successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Cache Clear Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
            ], 500);
        }
    }

    /**
     * Get benchmarking data
     */
    public function getBenchmarks(Request $request): JsonResponse
    {
        $request->validate([
            'metrics' => 'array',
            'metrics.*' => 'in:occupancy_rate,collection_rate,maintenance_efficiency,owner_satisfaction',
        ]);

        $metrics = $request->get('metrics', ['occupancy_rate', 'collection_rate']);

        try {
            $benchmarks = $this->calculateBenchmarks($metrics);
            
            return response()->json([
                'success' => true,
                'benchmarks' => $benchmarks,
                'industry_averages' => $this->getIndustryAverages($metrics),
            ]);
        } catch (\Exception $e) {
            Log::error('Benchmarks Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate benchmarks',
            ], 500);
        }
    }

    // Helper methods for new features

    private function getTodaysPayments(): float
    {
        return Payment::whereDate('payment_date', today())
            ->where('status', 'completed')
            ->sum('amount') ?? 0;
    }

    private function getPendingMaintenance(): int
    {
        return MaintenanceRequest::whereIn('status', ['pending', 'in_progress'])
            ->count();
    }

    private function getOverdueInvoices(): int
    {
        return Invoice::where('status', 'overdue')
            ->count();
    }

    private function getMonthlyRevenue(): float
    {
        return Invoice::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount') ?? 0;
    }

    private function getSpecificChartData(string $chartType, string $period, array $filters): array
    {
        switch ($chartType) {
            case 'revenue_trend':
                return $this->getRevenueTrendData($period, $filters);
            case 'occupancy_trend':
                return $this->getOccupancyTrendData($period, $filters);
            case 'payment_methods':
                return $this->getPaymentMethodsData($period, $filters);
            case 'maintenance_categories':
                return $this->getMaintenanceCategoriesData($period, $filters);
            case 'owner_satisfaction':
                return $this->getOwnerSatisfactionData($period, $filters);
            default:
                return [];
        }
    }

    private function getRevenueTrendData(string $period, array $filters): array
    {
        $startDate = $this->getStartDate($period);
        $revenues = Invoice::whereBetween('created_at', [$startDate, now()])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $revenues->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M j'))->toArray(),
            'data' => $revenues->pluck('total')->toArray(),
            'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
            'borderColor' => 'rgb(59, 130, 246)',
        ];
    }

    private function getOccupancyTrendData(string $period, array $filters): array
    {
        $startDate = $this->getStartDate($period);
        
        // Sample data - in production you'd calculate actual occupancy trends
        $dates = collect();
        $current = $startDate->copy();
        
        while ($current->lte(now()) && $dates->count() < 30) {
            $dates->push([
                'date' => $current->format('Y-m-d'),
                'label' => $current->format('M j'),
                'occupancy' => $this->getOccupancyForDate($current),
            ]);
            $current->addDay();
        }

        return [
            'labels' => $dates->pluck('label')->toArray(),
            'data' => $dates->pluck('occupancy')->toArray(),
            'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
            'borderColor' => 'rgb(16, 185, 129)',
        ];
    }

    private function getPaymentMethodsData(string $period, array $filters): array
    {
        $startDate = $this->getStartDate($period);
        $methods = Payment::whereBetween('payment_date', [$startDate, now()])
            ->where('status', 'completed')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        return [
            'labels' => $methods->pluck('payment_method')->toArray(),
            'data' => $methods->pluck('total')->toArray(),
            'backgroundColor' => [
                'rgba(239, 68, 68, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(139, 92, 246, 0.8)',
            ],
        ];
    }

    private function getMaintenanceCategoriesData(string $period, array $filters): array
    {
        $startDate = $this->getStartDate($period);
        $categories = MaintenanceRequest::whereBetween('created_at', [$startDate, now()])
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        return [
            'labels' => $categories->pluck('category')->toArray(),
            'data' => $categories->pluck('count')->toArray(),
            'backgroundColor' => [
                'rgba(239, 68, 68, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
            ],
        ];
    }

    private function getOwnerSatisfactionData(string $period, array $filters): array
    {
        $totalComplaints = Complaint::count();
        $resolvedComplaints = Complaint::where('status', 'resolved')->count();
        
        $satisfactionRate = $totalComplaints > 0 ? round(($resolvedComplaints / $totalComplaints) * 100, 1) : 100;

        return [
            'labels' => ['Satisfied', 'Neutral', 'Unsatisfied'],
            'data' => [$satisfactionRate, max(0, 100 - $satisfactionRate - 10), max(0, 10)],
            'backgroundColor' => [
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)',
            ],
        ];
    }

    private function getStartDate(string $period): Carbon
    {
        return match($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            'ytd' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }

    private function getOccupancyForDate(Carbon $date): float
    {
        $total = Apartment::count();
        $occupied = Apartment::where('status', 'occupied')->count();
        
        return $total > 0 ? round(($occupied / $total) * 100, 1) : 0;
    }

    private function getExportData(string $dataType, string $period, array $filters): array
    {
        switch ($dataType) {
            case 'financial':
                return $this->analyticsService->getFinancialOverview();
            case 'occupancy':
                return $this->analyticsService->getPropertyPerformance();
            case 'maintenance':
                return $this->analyticsService->getMaintenanceAnalytics();
            case 'owners':
                return $this->analyticsService->getOwnerAnalytics();
            default:
                return $this->analyticsService->getAdvancedDashboardAnalytics($period, $filters);
        }
    }

    private function exportToCSV(array $data, string $filename)
    {
        $csv = fopen('php://temp', 'w');
        
        fputcsv($csv, ['Metric', 'Value']);
        $this->flattenArrayForCSV($data, $csv);
        
        rewind($csv);
        $output = stream_get_contents($csv);
        fclose($csv);

        return Response::make($output, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ]);
    }

    private function flattenArrayForCSV(array $data, $csv, string $prefix = ''): void
    {
        foreach ($data as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $this->flattenArrayForCSV($value, $csv, $fullKey);
            } else {
                fputcsv($csv, [$fullKey, $value]);
            }
        }
    }

    private function calculateBenchmarks(array $metrics): array
    {
        $benchmarks = [];
        
        foreach ($metrics as $metric) {
            switch ($metric) {
                case 'occupancy_rate':
                    $current = $this->analyticsService->getPropertyPerformance()['occupancy_rate'] ?? 0;
                    $benchmarks[$metric] = [
                        'current' => $current,
                        'target' => 95,
                        'industry_avg' => 88,
                        'performance' => $current >= 90 ? 'excellent' : ($current >= 80 ? 'good' : 'needs_improvement'),
                    ];
                    break;
                case 'collection_rate':
                    $financialData = $this->analyticsService->getFinancialOverview();
                    $current = $financialData['collection_rate'] ?? 0;
                    $benchmarks[$metric] = [
                        'current' => $current,
                        'target' => 98,
                        'industry_avg' => 92,
                        'performance' => $current >= 95 ? 'excellent' : ($current >= 85 ? 'good' : 'needs_improvement'),
                    ];
                    break;
            }
        }
        
        return $benchmarks;
    }

    private function getIndustryAverages(array $metrics): array
    {
        return [
            'occupancy_rate' => 88,
            'collection_rate' => 92,
            'maintenance_efficiency' => 75,
            'owner_satisfaction' => 82,
        ];
    }
}
