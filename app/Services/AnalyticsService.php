<?php

namespace App\Services;

use App\Models\Apartment;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\Owner;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Enhanced Analytics Service with caching and error handling
 */
class AnalyticsService
{
    const CACHE_TTL = 300; // 5 minutes

    /**
     * Get financial overview with caching
     */
    public function getFinancialOverview()
    {
        return Cache::remember('analytics.financial_overview', self::CACHE_TTL, function () {
            try {
                $currentMonth = Carbon::now()->startOfMonth();
                $previousMonth = Carbon::now()->subMonth()->startOfMonth();
                $currentYear = Carbon::now()->startOfYear();

                // Current month revenue from completed payments
                $currentMonthRevenue = Payment::where('payment_date', '>=', $currentMonth)
                    ->where('status', 'completed')
                    ->sum('amount') ?? 0;

                // Previous month revenue for growth calculation
                $previousMonthRevenue = Payment::whereBetween('payment_date', [
                    $previousMonth, 
                    $previousMonth->copy()->endOfMonth()
                ])->where('status', 'completed')
                    ->sum('amount') ?? 0;

                // Calculate revenue growth
                $revenueGrowth = 0;
                if ($previousMonthRevenue > 0) {
                    $revenueGrowth = round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1);
                }

                // Annual revenue
                $annualRevenue = Payment::where('payment_date', '>=', $currentYear)
                    ->where('status', 'completed')
                    ->sum('amount') ?? 0;

                // Outstanding amount
                $outstandingAmount = Invoice::where('status', 'pending')
                    ->sum('total_amount') ?? 0;

                // Collection rate (current month)
                $totalInvoiced = Invoice::where('created_at', '>=', $currentMonth)
                    ->sum('total_amount') ?? 0;
                $collectionRate = $totalInvoiced > 0 ? round(($currentMonthRevenue / $totalInvoiced) * 100) : 0;

                // Payment methods breakdown
                $paymentMethods = Payment::where('payment_date', '>=', $currentMonth)
                    ->where('status', 'completed')
                    ->select('payment_method', DB::raw('SUM(amount) as total'))
                    ->groupBy('payment_method')
                    ->get();

                return [
                    'current_month_revenue' => $currentMonthRevenue,
                    'previous_month_revenue' => $previousMonthRevenue,
                    'revenue_growth' => $revenueGrowth,
                    'annual_revenue' => $annualRevenue,
                    'outstanding_amount' => $outstandingAmount,
                    'collection_rate' => $collectionRate,
                    'payment_methods' => $paymentMethods,
                ];
            } catch (\Exception $e) {
                Log::error('Financial Overview Error: ' . $e->getMessage());
                return $this->getEmptyFinancialOverview();
            }
        });
    }

    /**
     * Get property performance metrics
     */
    public function getPropertyPerformance()
    {
        return Cache::remember('analytics.property_performance', self::CACHE_TTL, function () {
            try {
                $totalApartments = Apartment::count();
                
                // Count occupied apartments through active leases (not apartment status)
                $occupiedApartments = Apartment::whereHas('leases', function($query) {
                    $query->where('status', 'active');
                })->count();
                
                $vacantApartments = $totalApartments - $occupiedApartments;
                $maintenanceApartments = Apartment::where('status', 'maintenance')->count();

                $occupancyRate = $totalApartments > 0 ? round(($occupiedApartments / $totalApartments) * 100, 1) : 0;

                // Revenue per apartment
                $revenuePerApartment = $occupiedApartments > 0 
                    ? Payment::where('payment_date', '>=', now()->startOfMonth())
                        ->where('status', 'completed')
                        ->sum('amount') / $occupiedApartments
                    : 0;

                // Top performing apartments
                $apartmentPerformance = $this->getApartmentPerformance();

                return [
                    'total_apartments' => $totalApartments,
                    'occupied_apartments' => $occupiedApartments,
                    'vacant_apartments' => $vacantApartments,
                    'maintenance_apartments' => $maintenanceApartments,
                    'occupancy_rate' => $occupancyRate,
                    'revenue_per_apartment' => round($revenuePerApartment, 2),
                    'apartment_performance' => $apartmentPerformance,
                ];
            } catch (\Exception $e) {
                Log::error('Property Performance Error: ' . $e->getMessage());
                return $this->getEmptyPropertyPerformance();
            }
        });
    }

    /**
     * Get tenant analytics
     */
    public function getTenantAnalytics()
    {
        return Cache::remember('analytics.tenant_analytics', self::CACHE_TTL, function () {
            try {
                $totalTenants = Tenant::where('status', 'active')->count();
                
                // Calculate retention rate (tenants who renewed)
                $renewedLeases = Lease::where('status', 'renewed')->count();
                $totalCompletedLeases = Lease::whereIn('status', ['expired', 'renewed', 'terminated'])->count();
                $retentionRate = $totalCompletedLeases > 0 ? round(($renewedLeases / $totalCompletedLeases) * 100, 1) : 0;

                // Payment behavior analysis
                $paymentBehavior = $this->analyzePaymentBehavior();

                // Tenant satisfaction (mock data - would come from surveys)
                $tenantSatisfaction = (object) ['avg_satisfaction' => 4.2];

                return [
                    'total_tenants' => $totalTenants,
                    'retention_rate' => $retentionRate,
                    'payment_behavior' => $paymentBehavior,
                    'tenant_satisfaction' => $tenantSatisfaction,
                ];
            } catch (\Exception $e) {
                Log::error('Tenant Analytics Error: ' . $e->getMessage());
                return $this->getEmptyTenantAnalytics();
            }
        });
    }

    /**
     * Get maintenance analytics
     */
    public function getMaintenanceAnalytics()
    {
        return Cache::remember('analytics.maintenance_analytics', self::CACHE_TTL, function () {
            try {
                $currentMonth = Carbon::now()->startOfMonth();
                
                $currentMonthRequests = MaintenanceRequest::where('created_at', '>=', $currentMonth)->count();
                
                // Status breakdown
                $statusBreakdown = MaintenanceRequest::select('status', DB::raw('COUNT(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray();

                // Response time stats
                $responseTimeStats = [
                    'average_response_time' => $this->calculateAverageResponseTime()
                ];

                // Cost analysis (mock data)
                $costAnalysis = (object) ['avg_cost' => 250.00];

                return [
                    'current_month_requests' => $currentMonthRequests,
                    'status_breakdown' => $statusBreakdown,
                    'response_time_stats' => $responseTimeStats,
                    'cost_analysis' => $costAnalysis,
                ];
            } catch (\Exception $e) {
                Log::error('Maintenance Analytics Error: ' . $e->getMessage());
                return $this->getEmptyMaintenanceAnalytics();
            }
        });
    }

    /**
     * Get monthly trends data
     */
    public function getMonthlyTrends()
    {
        return Cache::remember('analytics.monthly_trends', self::CACHE_TTL, function () {
            try {
                $trends = collect();
                
                for ($i = 11; $i >= 0; $i--) {
                    $month = Carbon::now()->subMonths($i);
                    $monthStart = $month->copy()->startOfMonth();
                    $monthEnd = $month->copy()->endOfMonth();
                    
                    $revenue = Payment::whereBetween('payment_date', [$monthStart, $monthEnd])
                        ->where('status', 'completed')
                        ->sum('amount') ?? 0;
                    
                    $expenses = 0; // Would calculate from expense records
                    
                    $maintenanceRequests = MaintenanceRequest::whereBetween('created_at', [$monthStart, $monthEnd])
                        ->count();
                    
                    $trends->push([
                        'month' => $month->format('M Y'),
                        'revenue' => (float) $revenue,
                        'expenses' => (float) $expenses,
                        'maintenance_requests' => $maintenanceRequests,
                    ]);
                }
                
                return $trends;
            } catch (\Exception $e) {
                Log::error('Monthly Trends Error: ' . $e->getMessage());
                return collect();
            }
        });
    }

    /**
     * Private helper methods
     */
    private function getApartmentPerformance()
    {
        return Apartment::with(['leases' => function($query) {
                $query->where('status', 'active');
            }])
            ->get()
            ->map(function($apartment) {
                $annualRevenue = Payment::whereHas('invoice', function($query) use ($apartment) {
                    $query->where('apartment_id', $apartment->id);
                })
                ->where('payment_date', '>=', now()->startOfYear())
                ->where('status', 'completed')
                ->sum('amount') ?? 0;

                return [
                    'apartment' => $apartment,
                    'annual_revenue' => $annualRevenue,
                    'payment_count' => $apartment->invoices()->count(),
                    'occupancy_months' => 12, // Mock data
                ];
            })
            ->sortByDesc('annual_revenue')
            ->take(10)
            ->values()
            ->toArray();
    }

    private function analyzePaymentBehavior()
    {
        // Mock implementation - would analyze actual payment patterns
        return [
            'always_on_time' => 85,
            'usually_late' => 12,
            'frequent_defaulters' => 3,
        ];
    }

    private function calculateAverageResponseTime()
    {
        // Mock implementation - would calculate from actual maintenance request data
        return 2.5;
    }

    // Empty data fallbacks for error cases
    private function getEmptyFinancialOverview()
    {
        return [
            'current_month_revenue' => 0,
            'previous_month_revenue' => 0,
            'revenue_growth' => 0,
            'annual_revenue' => 0,
            'outstanding_amount' => 0,
            'collection_rate' => 0,
            'payment_methods' => collect(),
        ];
    }

    private function getEmptyPropertyPerformance()
    {
        return [
            'total_apartments' => 0,
            'occupied_apartments' => 0,
            'vacant_apartments' => 0,
            'maintenance_apartments' => 0,
            'occupancy_rate' => 0,
            'revenue_per_apartment' => 0,
            'apartment_performance' => [],
        ];
    }

    private function getEmptyTenantAnalytics()
    {
        return [
            'total_tenants' => 0,
            'retention_rate' => 0,
            'payment_behavior' => ['always_on_time' => 0, 'usually_late' => 0, 'frequent_defaulters' => 0],
            'tenant_satisfaction' => (object) ['avg_satisfaction' => 0],
        ];
    }

    private function getEmptyMaintenanceAnalytics()
    {
        return [
            'current_month_requests' => 0,
            'status_breakdown' => [],
            'response_time_stats' => ['average_response_time' => 0],
            'cost_analysis' => (object) ['avg_cost' => 0],
        ];
    }

    /**
     * Clear all analytics cache
     */
    public function clearCache()
    {
        Cache::forget('analytics.financial_overview');
        Cache::forget('analytics.property_performance');
        Cache::forget('analytics.tenant_analytics');
        Cache::forget('analytics.maintenance_analytics');
        Cache::forget('analytics.monthly_trends');
        Cache::forget('analytics.advanced_dashboard');
        Cache::forget('analytics.comprehensive_report');
    }

    /**
     * Get comprehensive dashboard analytics with advanced metrics
     */
    public function getAdvancedDashboardAnalytics(string $period = 'month', array $filters = []): array
    {
        $cacheKey = 'analytics.advanced_dashboard.' . md5($period . serialize($filters));
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($period, $filters) {
            try {
                $startDate = $this->getStartDate($period);
                $endDate = now();

                return [
                    'period_info' => [
                        'period' => $period,
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date' => $endDate->format('Y-m-d'),
                        'days_in_period' => $startDate->diffInDays($endDate),
                    ],
                    'financial_deep_dive' => $this->getFinancialDeepDive($startDate, $endDate, $filters),
                    'occupancy_trends' => $this->getOccupancyTrends($startDate, $endDate, $filters),
                    'payment_insights' => $this->getPaymentInsights($startDate, $endDate, $filters),
                    'maintenance_efficiency' => $this->getMaintenanceEfficiency($startDate, $endDate, $filters),
                    'tenant_lifecycle' => $this->getTenantLifecycleAnalytics($startDate, $endDate, $filters),
                    'predictive_metrics' => $this->getPredictiveMetrics($startDate, $endDate, $filters),
                    'comparative_analysis' => $this->getComparativeAnalysis($period, $filters),
                    'performance_indicators' => $this->getAdvancedKPIs($startDate, $endDate, $filters),
                ];
            } catch (\Exception $e) {
                Log::error('Advanced Dashboard Analytics Error: ' . $e->getMessage());
                return $this->getEmptyAdvancedAnalytics();
            }
        });
    }

    /**
     * Financial Deep Dive Analysis
     */
    private function getFinancialDeepDive(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        // Revenue breakdown by multiple dimensions
        $revenueBreakdown = [
            'by_type' => Invoice::whereBetween('created_at', [$startDate, $endDate])
                ->select('type', DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as count'))
                ->groupBy('type')
                ->get()
                ->keyBy('type'),
            
            'by_floor' => Invoice::join('apartments', 'invoices.apartment_id', '=', 'apartments.id')
                ->whereBetween('invoices.created_at', [$startDate, $endDate])
                ->select('apartments.floor', DB::raw('SUM(invoices.total_amount) as total'))
                ->groupBy('apartments.floor')
                ->orderBy('apartments.floor')
                ->get(),
            
            'by_month' => Invoice::whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(total_amount) as total'),
                    DB::raw('COUNT(*) as invoice_count')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'month')
                ->get()
                ->map(function ($item) {
                    return [
                        'period' => Carbon::create($item->year, $item->month)->format('M Y'),
                        'revenue' => $item->total,
                        'invoice_count' => $item->invoice_count,
                        'avg_invoice_value' => $item->invoice_count > 0 ? $item->total / $item->invoice_count : 0,
                    ];
                }),
        ];

        // Cash flow analysis
        $cashFlow = [
            'inflow' => Payment::whereBetween('payment_date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('amount'),
            'projected_inflow' => Invoice::where('status', 'pending')
                ->whereBetween('due_date', [$startDate, $endDate->copy()->addMonth()])
                ->sum('total_amount'),
            'overdue_amounts' => Invoice::where('status', 'overdue')
                ->sum('total_amount'),
        ];

        // Profitability analysis
        $profitability = [
            'gross_revenue' => $revenueBreakdown['by_type']->sum('total'),
            'collection_efficiency' => $this->calculateCollectionEfficiency($startDate, $endDate),
            'revenue_per_sqft' => $this->calculateRevenuePerSqft($startDate, $endDate),
            'year_over_year_growth' => $this->calculateYearOverYearGrowth($startDate, $endDate),
        ];

        return [
            'revenue_breakdown' => $revenueBreakdown,
            'cash_flow' => $cashFlow,
            'profitability' => $profitability,
        ];
    }

    /**
     * Occupancy Trends Analysis
     */
    private function getOccupancyTrends(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $occupancyHistory = collect();
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            $occupiedCount = Lease::where('status', 'active')
                ->where('start_date', '<=', $current)
                ->where('end_date', '>=', $current)
                ->count();
            
            $totalUnits = Apartment::count();
            
            $occupancyHistory->push([
                'date' => $current->format('Y-m-d'),
                'occupied_units' => $occupiedCount,
                'total_units' => $totalUnits,
                'occupancy_rate' => $totalUnits > 0 ? ($occupiedCount / $totalUnits) * 100 : 0,
            ]);
            
            $current->addDays(7); // Weekly snapshots
        }

        // Lease expiry forecast
        $leaseExpiryForecast = Lease::where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addMonths(12)])
            ->select(
                DB::raw('YEAR(end_date) as year'),
                DB::raw('MONTH(end_date) as month'),
                DB::raw('COUNT(*) as expiring_count'),
                DB::raw('SUM(monthly_rent) as revenue_at_risk')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'month')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => Carbon::create($item->year, $item->month)->format('M Y'),
                    'expiring_leases' => $item->expiring_count,
                    'revenue_at_risk' => $item->revenue_at_risk,
                ];
            });

        // Vacancy analysis
        $vacancyAnalysis = [
            'current_vacancies' => Apartment::whereDoesntHave('leases', function($query) {
                $query->where('status', 'active');
            })->count(),
            'avg_vacancy_duration' => $this->calculateAvgVacancyDuration(),
            'vacancy_cost' => $this->calculateVacancyCost($startDate, $endDate),
        ];

        return [
            'occupancy_history' => $occupancyHistory,
            'lease_expiry_forecast' => $leaseExpiryForecast,
            'vacancy_analysis' => $vacancyAnalysis,
        ];
    }

    /**
     * Payment Insights Analysis
     */
    private function getPaymentInsights(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        // Payment patterns
        $paymentPatterns = [
            'on_time_payments' => Payment::join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                ->whereBetween('payments.payment_date', [$startDate, $endDate])
                ->whereRaw('payments.payment_date <= invoices.due_date')
                ->count(),
            'late_payments' => Payment::join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                ->whereBetween('payments.payment_date', [$startDate, $endDate])
                ->whereRaw('payments.payment_date > invoices.due_date')
                ->count(),
            'partial_payments' => Payment::whereBetween('payment_date', [$startDate, $endDate])
                ->where('payment_type', 'partial')
                ->count(),
        ];

        // Payment method analysis
        $paymentMethodTrends = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('AVG(amount) as avg_amount')
            )
            ->groupBy('payment_method')
            ->get();

        // Tenant payment behavior
        $tenantPaymentBehavior = Payment::join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('users', 'invoices.tenant_id', '=', 'users.id')
            ->whereBetween('payments.payment_date', [$startDate, $endDate])
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(*) as payment_count'),
                DB::raw('SUM(CASE WHEN payments.payment_date <= invoices.due_date THEN 1 ELSE 0 END) as on_time_count'),
                DB::raw('AVG(TIMESTAMPDIFF(DAY, invoices.due_date, payments.payment_date)) as avg_delay_days')
            )
            ->groupBy('users.id', 'users.name')
            ->get()
            ->map(function ($item) {
                return [
                    'tenant_id' => $item->id,
                    'tenant_name' => $item->name,
                    'payment_count' => $item->payment_count,
                    'on_time_rate' => $item->payment_count > 0 ? ($item->on_time_count / $item->payment_count) * 100 : 0,
                    'avg_delay_days' => max(0, $item->avg_delay_days),
                ];
            });

        return [
            'payment_patterns' => $paymentPatterns,
            'payment_method_trends' => $paymentMethodTrends,
            'tenant_behavior' => $tenantPaymentBehavior,
        ];
    }

    /**
     * Maintenance Efficiency Analysis
     */
    private function getMaintenanceEfficiency(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        // Response time analysis
        $responseTimeStats = MaintenanceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->selectRaw('
                AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_resolution_hours,
                MIN(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as min_resolution_hours,
                MAX(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as max_resolution_hours,
                COUNT(*) as completed_requests
            ')
            ->first();

        // Cost analysis
        $costAnalysis = MaintenanceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'priority',
                'category',
                DB::raw('COUNT(*) as request_count'),
                DB::raw('AVG(COALESCE(actual_cost, estimated_cost)) as avg_cost'),
                DB::raw('SUM(COALESCE(actual_cost, estimated_cost)) as total_cost')
            )
            ->groupBy('priority', 'category')
            ->get();

        // Maintenance trends by apartment
        $apartmentMaintenanceTrends = MaintenanceRequest::join('apartments', 'maintenance_requests.apartment_id', '=', 'apartments.id')
            ->whereBetween('maintenance_requests.created_at', [$startDate, $endDate])
            ->select(
                'apartments.id',
                'apartments.number',
                'apartments.floor',
                DB::raw('COUNT(*) as request_count'),
                DB::raw('SUM(COALESCE(actual_cost, estimated_cost)) as total_cost')
            )
            ->groupBy('apartments.id', 'apartments.number', 'apartments.floor')
            ->orderBy('request_count', 'desc')
            ->limit(10)
            ->get();

        return [
            'response_time_stats' => $responseTimeStats,
            'cost_analysis' => $costAnalysis,
            'apartment_trends' => $apartmentMaintenanceTrends,
        ];
    }

    /**
     * Tenant Lifecycle Analytics
     */
    private function getTenantLifecycleAnalytics(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        // New tenant acquisition
        $newTenants = Lease::whereBetween('start_date', [$startDate, $endDate])
            ->with(['tenant', 'apartment'])
            ->get()
            ->map(function ($lease) {
                return [
                    'tenant_name' => $lease->tenant->name,
                    'apartment' => $lease->apartment->number,
                    'start_date' => $lease->start_date->format('Y-m-d'),
                    'monthly_rent' => $lease->monthly_rent,
                ];
            });

        // Tenant retention analysis
        $retentionAnalysis = [
            'lease_renewals' => Lease::where('status', 'active')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('leases as prev_leases')
                        ->whereColumn('prev_leases.tenant_id', 'leases.tenant_id')
                        ->whereColumn('prev_leases.apartment_id', 'leases.apartment_id')
                        ->where('prev_leases.status', 'completed')
                        ->whereRaw('prev_leases.end_date = leases.start_date');
                })
                ->count(),
            'average_tenancy_duration' => Lease::where('status', 'completed')
                ->selectRaw('AVG(TIMESTAMPDIFF(MONTH, start_date, end_date)) as avg_months')
                ->value('avg_months'),
        ];

        // Tenant value analysis
        $tenantValueAnalysis = Payment::join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('users', 'invoices.tenant_id', '=', 'users.id')
            ->where('payments.status', 'completed')
            ->select(
                'users.id',
                'users.name',
                DB::raw('SUM(payments.amount) as total_payments'),
                DB::raw('COUNT(DISTINCT invoices.id) as invoice_count'),
                DB::raw('MIN(payments.payment_date) as first_payment'),
                DB::raw('MAX(payments.payment_date) as last_payment')
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_payments', 'desc')
            ->limit(10)
            ->get();

        return [
            'new_tenants' => $newTenants,
            'retention_analysis' => $retentionAnalysis,
            'tenant_value_analysis' => $tenantValueAnalysis,
        ];
    }

    /**
     * Predictive Metrics
     */
    private function getPredictiveMetrics(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        // Revenue forecast based on trends
        $monthlyRevenues = Invoice::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
        ->where('created_at', '>=', now()->subYear())
        ->groupBy('year', 'month')
        ->orderBy('year', 'month')
        ->get();

        $avgMonthlyGrowth = 0;
        if ($monthlyRevenues->count() > 1) {
            $growthRates = [];
            for ($i = 1; $i < $monthlyRevenues->count(); $i++) {
                $current = $monthlyRevenues[$i]->total;
                $previous = $monthlyRevenues[$i-1]->total;
                if ($previous > 0) {
                    $growthRates[] = ($current - $previous) / $previous;
                }
            }
            $avgMonthlyGrowth = count($growthRates) > 0 ? array_sum($growthRates) / count($growthRates) : 0;
        }

        $currentRevenue = $monthlyRevenues->last()->total ?? 0;
        $projectedRevenue = [];
        for ($i = 1; $i <= 6; $i++) {
            $currentRevenue *= (1 + $avgMonthlyGrowth);
            $projectedRevenue[] = [
                'month' => now()->addMonths($i)->format('M Y'),
                'projected_revenue' => round($currentRevenue, 2),
            ];
        }

        // Occupancy forecast
        $occupancyForecast = $this->calculateOccupancyForecast();

        // Maintenance cost predictions
        $avgMonthlyCost = MaintenanceRequest::where('created_at', '>=', now()->subYear())
            ->selectRaw('AVG(monthly_cost) as avg_cost')
            ->from(DB::raw('(
                SELECT YEAR(created_at) as year, MONTH(created_at) as month, 
                       SUM(COALESCE(actual_cost, estimated_cost)) as monthly_cost
                FROM maintenance_requests 
                WHERE created_at >= "' . now()->subYear()->format('Y-m-d') . '"
                GROUP BY YEAR(created_at), MONTH(created_at)
            ) as monthly_costs'))
            ->value('avg_cost') ?? 0;

        return [
            'revenue_forecast' => $projectedRevenue,
            'occupancy_forecast' => $occupancyForecast,
            'predicted_maintenance_cost' => $avgMonthlyCost,
            'growth_indicators' => [
                'monthly_growth_rate' => $avgMonthlyGrowth * 100,
                'projected_annual_revenue' => $currentRevenue * 12,
            ],
        ];
    }

    /**
     * Comparative Analysis
     */
    private function getComparativeAnalysis(string $period, array $filters): array
    {
        $currentPeriod = $this->getStartDate($period);
        $previousPeriod = $this->getPreviousPeriodStart($period);

        $currentMetrics = $this->getPeriodMetrics($currentPeriod, now(), $filters);
        $previousMetrics = $this->getPeriodMetrics($previousPeriod, $currentPeriod->copy()->subDay(), $filters);

        return [
            'current_period' => $currentMetrics,
            'previous_period' => $previousMetrics,
            'comparisons' => [
                'revenue_change' => $this->calculateChangePercentage($currentMetrics['revenue'], $previousMetrics['revenue']),
                'occupancy_change' => $this->calculateChangePercentage($currentMetrics['occupancy_rate'], $previousMetrics['occupancy_rate']),
                'collection_change' => $this->calculateChangePercentage($currentMetrics['collection_rate'], $previousMetrics['collection_rate']),
                'maintenance_change' => $this->calculateChangePercentage($currentMetrics['maintenance_requests'], $previousMetrics['maintenance_requests']),
            ],
        ];
    }

    /**
     * Advanced KPIs
     */
    private function getAdvancedKPIs(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        return [
            'financial_health' => [
                'revenue_per_unit' => $this->calculateRevenuePerUnit($startDate, $endDate),
                'collection_efficiency' => $this->calculateCollectionEfficiency($startDate, $endDate),
                'operating_margin' => $this->calculateOperatingMargin($startDate, $endDate),
                'cash_conversion_cycle' => $this->calculateCashConversionCycle($startDate, $endDate),
            ],
            'operational_excellence' => [
                'tenant_satisfaction_score' => $this->calculateTenantSatisfactionScore($startDate, $endDate),
                'maintenance_efficiency' => $this->calculateMaintenanceEfficiency($startDate, $endDate),
                'lease_renewal_rate' => $this->calculateLeaseRenewalRate($startDate, $endDate),
                'average_tenancy_duration' => $this->calculateAverageTenancyDuration(),
            ],
            'growth_metrics' => [
                'revenue_growth_rate' => $this->calculateRevenueGrowthRate($startDate, $endDate),
                'occupancy_trend' => $this->calculateOccupancyTrend($startDate, $endDate),
                'market_penetration' => $this->calculateMarketPenetration(),
                'tenant_acquisition_cost' => $this->calculateTenantAcquisitionCost($startDate, $endDate),
            ],
        ];
    }

    // Helper methods for advanced analytics
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

    private function getPreviousPeriodStart(string $period): Carbon
    {
        return match($period) {
            'week' => now()->subWeek()->startOfWeek(),
            'month' => now()->subMonth()->startOfMonth(),
            'quarter' => now()->subQuarter()->startOfQuarter(),
            'year' => now()->subYear()->startOfYear(),
            'ytd' => now()->subYear()->startOfYear(),
            default => now()->subMonth()->startOfMonth(),
        };
    }

    private function calculateChangePercentage($current, $previous): float
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 2);
    }

    private function calculateCollectionEfficiency(Carbon $startDate, Carbon $endDate): float
    {
        $totalInvoiced = Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $totalCollected = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'completed')->sum('amount');
        return $totalInvoiced > 0 ? round(($totalCollected / $totalInvoiced) * 100, 2) : 0;
    }

    private function calculateRevenuePerSqft(Carbon $startDate, Carbon $endDate): float
    {
        $totalRevenue = Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $totalSqft = Apartment::sum('size') ?? 1;
        return round($totalRevenue / $totalSqft, 2);
    }

    private function calculateYearOverYearGrowth(Carbon $startDate, Carbon $endDate): float
    {
        $currentYear = Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $previousYear = Invoice::whereBetween('created_at', [
            $startDate->copy()->subYear(),
            $endDate->copy()->subYear()
        ])->sum('total_amount');
        
        return $this->calculateChangePercentage($currentYear, $previousYear);
    }

    private function calculateAvgVacancyDuration(): float
    {
        // This would require vacancy tracking - simplified for now
        return 21.5; // Average days vacant
    }

    private function calculateVacancyCost(Carbon $startDate, Carbon $endDate): float
    {
        $vacantUnits = Apartment::whereDoesntHave('leases', function($query) {
            $query->where('status', 'active');
        })->count();
        
        $avgRent = Lease::where('status', 'active')->avg('monthly_rent') ?? 0;
        return $vacantUnits * $avgRent;
    }

    private function getEmptyAdvancedAnalytics(): array
    {
        return [
            'period_info' => ['period' => 'month', 'start_date' => now()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')],
            'financial_deep_dive' => ['revenue_breakdown' => [], 'cash_flow' => [], 'profitability' => []],
            'occupancy_trends' => ['occupancy_history' => [], 'lease_expiry_forecast' => [], 'vacancy_analysis' => []],
            'payment_insights' => ['payment_patterns' => [], 'payment_method_trends' => [], 'tenant_behavior' => []],
            'maintenance_efficiency' => ['response_time_stats' => null, 'cost_analysis' => [], 'apartment_trends' => []],
            'tenant_lifecycle' => ['new_tenants' => [], 'retention_analysis' => [], 'tenant_value_analysis' => []],
            'predictive_metrics' => ['revenue_forecast' => [], 'occupancy_forecast' => [], 'growth_indicators' => []],
            'comparative_analysis' => ['current_period' => [], 'previous_period' => [], 'comparisons' => []],
            'performance_indicators' => ['financial_health' => [], 'operational_excellence' => [], 'growth_metrics' => []],
        ];
    }

    // Additional KPI calculation methods (simplified implementations)
    private function calculateRevenuePerUnit(Carbon $startDate, Carbon $endDate): float
    {
        $totalRevenue = Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $totalUnits = Apartment::count();
        return $totalUnits > 0 ? round($totalRevenue / $totalUnits, 2) : 0;
    }

    private function calculateOperatingMargin(Carbon $startDate, Carbon $endDate): float
    {
        $revenue = Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $maintenanceCosts = MaintenanceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->sum(DB::raw('COALESCE(actual_cost, estimated_cost)')) ?? 0;
        return $revenue > 0 ? round((($revenue - $maintenanceCosts) / $revenue) * 100, 2) : 0;
    }

    private function calculateCashConversionCycle(Carbon $startDate, Carbon $endDate): float
    {
        // Simplified - would need more detailed cash flow tracking
        return 15.5; // Average days from invoice to payment
    }

    private function calculateTenantSatisfactionScore(Carbon $startDate, Carbon $endDate): float
    {
        // Based on maintenance response times and complaint resolution
        $avgResponseTime = MaintenanceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->avg(DB::raw('TIMESTAMPDIFF(HOUR, created_at, completed_at)')) ?? 48;
        
        $baseScore = 100;
        $penalty = min(50, ($avgResponseTime - 24) * 2); // Penalize for response time > 24 hours
        return max(0, round($baseScore - $penalty, 1));
    }

    private function calculateMaintenanceEfficiency(Carbon $startDate, Carbon $endDate): float
    {
        $totalRequests = MaintenanceRequest::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedRequests = MaintenanceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')->count();
        return $totalRequests > 0 ? round(($completedRequests / $totalRequests) * 100, 2) : 0;
    }

    private function calculateLeaseRenewalRate(Carbon $startDate, Carbon $endDate): float
    {
        $expiredLeases = Lease::whereBetween('end_date', [$startDate, $endDate])
            ->where('status', 'completed')->count();
        $renewedLeases = Lease::whereBetween('start_date', [$startDate, $endDate])
            ->whereExists(function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw(1))
                    ->from('leases as prev_leases')
                    ->whereColumn('prev_leases.tenant_id', 'leases.tenant_id')
                    ->whereColumn('prev_leases.apartment_id', 'leases.apartment_id')
                    ->whereBetween('prev_leases.end_date', [$startDate, $endDate])
                    ->where('prev_leases.status', 'completed');
            })->count();
        
        return $expiredLeases > 0 ? round(($renewedLeases / $expiredLeases) * 100, 2) : 0;
    }

    private function calculateAverageTenancyDuration(): float
    {
        return Lease::where('status', 'completed')
            ->selectRaw('AVG(TIMESTAMPDIFF(MONTH, start_date, end_date)) as avg_months')
            ->value('avg_months') ?? 12;
    }

    private function calculateRevenueGrowthRate(Carbon $startDate, Carbon $endDate): float
    {
        $currentRevenue = Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $previousPeriodStart = $this->getPreviousPeriodStart('month');
        $previousRevenue = Invoice::whereBetween('created_at', [
            $previousPeriodStart,
            $previousPeriodStart->copy()->addMonth()
        ])->sum('total_amount');
        
        return $this->calculateChangePercentage($currentRevenue, $previousRevenue);
    }

    private function calculateOccupancyTrend(Carbon $startDate, Carbon $endDate): float
    {
        $currentOccupancy = Apartment::whereHas('leases', function($query) {
            $query->where('status', 'active');
        })->count();
        
        $previousOccupancy = Apartment::whereHas('leases', function($query) use ($startDate) {
            $query->where('status', 'active')
                ->where('start_date', '<=', $startDate->copy()->subMonth())
                ->where('end_date', '>=', $startDate->copy()->subMonth());
        })->count();
        
        return $this->calculateChangePercentage($currentOccupancy, $previousOccupancy);
    }

    private function calculateMarketPenetration(): float
    {
        // This would require market data - simplified
        return 75.5; // Percentage of local market captured
    }

    private function calculateTenantAcquisitionCost(Carbon $startDate, Carbon $endDate): float
    {
        // This would require marketing/advertising cost tracking - simplified
        $newTenants = Lease::whereBetween('start_date', [$startDate, $endDate])->count();
        $estimatedMarketingCosts = 500; // Per new tenant
        return $newTenants > 0 ? $estimatedMarketingCosts : 0;
    }

    private function calculateOccupancyForecast(): array
    {
        $forecast = [];
        $currentOccupancy = Apartment::whereHas('leases', function($query) {
            $query->where('status', 'active');
        })->count();
        
        for ($i = 1; $i <= 6; $i++) {
            $forecastDate = now()->addMonths($i);
            $expiringLeases = Lease::where('status', 'active')
                ->whereYear('end_date', $forecastDate->year)
                ->whereMonth('end_date', $forecastDate->month)
                ->count();
            
            // Assume 80% renewal rate
            $renewals = round($expiringLeases * 0.8);
            $currentOccupancy = $currentOccupancy - $expiringLeases + $renewals;
            
            $forecast[] = [
                'month' => $forecastDate->format('M Y'),
                'projected_occupancy' => max(0, $currentOccupancy),
                'occupancy_rate' => round(($currentOccupancy / Apartment::count()) * 100, 1),
            ];
        }
        
        return $forecast;
    }

    private function getPeriodMetrics(Carbon $start, Carbon $end, array $filters): array
    {
        return [
            'revenue' => Invoice::whereBetween('created_at', [$start, $end])->sum('total_amount'),
            'occupancy_rate' => (Apartment::whereHas('leases', function($query) {
                $query->where('status', 'active');
            })->count() / Apartment::count()) * 100,
            'collection_rate' => $this->calculateCollectionEfficiency($start, $end),
            'maintenance_requests' => MaintenanceRequest::whereBetween('created_at', [$start, $end])->count(),
        ];
    }
}
