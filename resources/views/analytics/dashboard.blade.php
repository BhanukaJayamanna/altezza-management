<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                    {{ __('Analytics Dashboard') }}
                </h2>
                <p class="text-slate-600 text-sm mt-1">Comprehensive analytics and insights for your property management.</p>
            </div>
        </div>
    </x-slot>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/analytics.css') }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/altezza-analytics.js') }}"></script>
@endpush
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
                    <p class="mt-2 text-gray-600">
                        <span class="real-time-indicator"></span>
                        Real-time insights for {{ $recent_period ?? now()->format('F Y') }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button id="refresh-data" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                    <div class="relative export-dropdown">
                        <button id="export-dropdown" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                        <div id="export-menu" class="export-menu hidden">
                            <a href="#" class="export-option" data-format="csv">
                                <i class="fas fa-file-csv"></i>CSV Export
                            </a>
                            <a href="#" class="export-option" data-format="pdf">
                                <i class="fas fa-file-pdf"></i>PDF Report
                            </a>
                            <a href="#" class="export-option" data-format="excel">
                                <i class="fas fa-file-excel"></i>Excel Export
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($error))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                {{ $error }}
            </div>
        @endif

        <!-- Key Performance Indicators -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Monthly Revenue</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">LKR {{ number_format($analytics['financial_overview']['current_month_revenue'] ?? 0) }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-home text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Occupancy Rate</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $analytics['property_performance']['occupancy_rate'] ?? 0 }}%</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-tools text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Maintenance Requests</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $analytics['maintenance_analytics']['current_month_requests'] ?? 0 }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Owners</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $analytics['owner_analytics']['total_owners'] ?? 0 }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="filter-controls mb-8">
            <div class="filter-group">
                <div class="filter-item">
                    <label for="period-filter" class="filter-label">Period</label>
                    <select id="period-filter" class="filter-select">
                        <option value="week">This Week</option>
                        <option value="month" selected>This Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="apartment-type-filter" class="filter-label">Apartment Type</label>
                    <select id="apartment-type-filter" class="filter-select">
                        <option value="">All Types</option>
                        <option value="1BHK">1BHK</option>
                        <option value="2BHK">2BHK</option>
                        <option value="3BHK">3BHK</option>
                        <option value="4BHK">4BHK</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="floor-filter" class="filter-label">Floor</label>
                    <select id="floor-filter" class="filter-select">
                        <option value="">All Floors</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">Floor {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="filter-item">
                    <label for="building-filter" class="filter-label">Building</label>
                    <select id="building-filter" class="filter-select">
                        <option value="">All Buildings</option>
                        <option value="A">Building A</option>
                        <option value="B">Building B</option>
                        <option value="C">Building C</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Real-time Metrics -->
        <div id="real-time-metrics" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
            <!-- Real-time metrics will be populated by JavaScript -->
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-line"></i>
                        Revenue Trend
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="revenue-trend-chart"></canvas>
                    <div id="revenue-loading" class="chart-loading hidden">
                        <div class="spinner"></div>
                        <p class="text-sm text-gray-600">Loading revenue data...</p>
                    </div>
                </div>
            </div>

            <!-- Occupancy Chart -->
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-area"></i>
                        Occupancy Trend
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="occupancy-trend-chart"></canvas>
                    <div id="occupancy-loading" class="chart-loading hidden">
                        <div class="spinner"></div>
                        <p class="text-sm text-gray-600">Loading occupancy data...</p>
                    </div>
                </div>
            </div>

            <!-- Payment Methods Chart -->
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-pie"></i>
                        Payment Methods
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="payment-methods-chart"></canvas>
                    <div id="payment-loading" class="chart-loading hidden">
                        <div class="spinner"></div>
                        <p class="text-sm text-gray-600">Loading payment data...</p>
                    </div>
                </div>
            </div>

            <!-- Maintenance Categories Chart -->
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-bar"></i>
                        Maintenance Categories
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="maintenance-categories-chart"></canvas>
                    <div id="maintenance-loading" class="chart-loading hidden">
                        <div class="spinner"></div>
                        <p class="text-sm text-gray-600">Loading maintenance data...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Owner Satisfaction Chart -->
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-smile"></i>
                        Owner Satisfaction
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="owner-satisfaction-chart"></canvas>
                    <div id="satisfaction-loading" class="chart-loading hidden">
                        <div class="spinner"></div>
                        <p class="text-sm text-gray-600">Loading satisfaction data...</p>
                    </div>
                </div>
            </div>

            <!-- Advanced Analytics Load Button -->
            <div class="bg-white rounded-lg p-6 shadow-sm flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-analytics text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Advanced Analytics</h3>
                    <p class="text-gray-600 mb-4">Load detailed insights and predictive analytics</p>
                    <button id="load-advanced" class="btn-primary">
                        <i class="fas fa-chart-line mr-2"></i>
                        Load Advanced Analytics
                    </button>
                </div>
            </div>
        </div>

        <!-- Advanced Analytics Content (Hidden by default) -->
        <div id="advanced-analytics-content" class="hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Financial Deep Dive -->
                <div class="advanced-analytics-section">
                    <h3 class="advanced-analytics-title">
                        <i class="fas fa-chart-line"></i>
                        Financial Deep Dive
                    </h3>
                    <div id="financial-metrics">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>

                <!-- Occupancy Insights -->
                <div class="advanced-analytics-section">
                    <h3 class="advanced-analytics-title">
                        <i class="fas fa-home"></i>
                        Occupancy Insights
                    </h3>
                    <div id="occupancy-metrics">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>

                <!-- Predictive Analytics -->
                <div class="advanced-analytics-section">
                    <h3 class="advanced-analytics-title">
                        <i class="fas fa-crystal-ball"></i>
                        Predictive Analytics
                    </h3>
                    <div id="predictive-metrics">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trends Table -->
        @if(isset($analytics['monthly_trends']) && count($analytics['monthly_trends']) > 0)
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Trends</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expenses</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Maintenance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Owners</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($analytics['monthly_trends'] as $month)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $month['month'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">LKR {{ number_format($month['revenue']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">LKR {{ number_format($month['expenses']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $month['maintenance_requests'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $month['new_owners'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Analytics Dashboard Loaded');
    
    // Set up real-time indicator animation
    const indicator = document.querySelector('.real-time-indicator');
    if (indicator) {
        setInterval(() => {
            indicator.style.opacity = indicator.style.opacity === '0.5' ? '1' : '0.5';
        }, 1000);
    }
    
    // Set up refresh button
    const refreshBtn = document.getElementById('refresh-data');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            location.reload();
        });
    }
});
</script>

<style>
.real-time-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    margin-right: 0.5rem;
    transition: opacity 0.3s ease;
}
</style>
@endsection
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Current Month Revenue -->
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">This Month Revenue</p>
                                <p class="text-2xl font-bold">LKR {{ number_format($financial_overview['current_month_revenue'], 2) }}</p>
                                @if($financial_overview['revenue_growth'] != 0)
                                    <div class="flex items-center mt-2">
                                        @if($financial_overview['revenue_growth'] > 0)
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm">+{{ $financial_overview['revenue_growth'] }}%</span>
                                        @else
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 112 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm">{{ $financial_overview['revenue_growth'] }}%</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Annual Revenue -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Annual Revenue</p>
                                <p class="text-2xl font-bold">LKR {{ number_format($financial_overview['annual_revenue'], 2) }}</p>
                                <p class="text-sm opacity-75 mt-2">{{ date('Y') }} Total</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H7zm2 0a1 1 0 100 2h.01a1 1 0 100-2H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding Amount -->
                    <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Outstanding</p>
                                <p class="text-2xl font-bold">LKR {{ number_format($financial_overview['outstanding_amount'], 2) }}</p>
                                <p class="text-sm opacity-75 mt-2">Pending Collection</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Collection Rate -->
                    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Collection Rate</p>
                                <p class="text-2xl font-bold">{{ $financial_overview['collection_rate'] }}%</p>
                                <p class="text-sm opacity-75 mt-2">This Month</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Revenue Trends Chart -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">12-Month Revenue Trend</h4>
                        <div class="h-64">
                            <canvas id="revenueTrendChart"></canvas>
                        </div>
                    </div>

                    <!-- Payment Methods Breakdown -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Payment Methods</h4>
                        <div class="h-64">
                            <canvas id="paymentMethodsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Performance Section -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Property Performance
                </h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Occupancy Rate -->
                    <div class="text-center">
                        <div class="relative inline-flex items-center justify-center w-32 h-32 mb-4">
                            <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-gray-200" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path class="text-blue-600" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="{{ $property_performance['occupancy_rate'] }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-2xl font-bold text-gray-900">{{ $property_performance['occupancy_rate'] }}%</span>
                            </div>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Occupancy Rate</h4>
                        <p class="text-sm text-gray-500">{{ $property_performance['occupied_apartments'] }} of {{ $property_performance['total_apartments'] }} units</p>
                    </div>

                    <!-- Revenue per Unit -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">LKR {{ number_format($property_performance['revenue_per_apartment'], 2) }}</h4>
                        <p class="text-sm text-gray-500">Revenue per Unit</p>
                    </div>

                    <!-- Vacant Units -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">{{ $property_performance['vacant_apartments'] }}</h4>
                        <p class="text-sm text-gray-500">Vacant Units</p>
                    </div>

                    <!-- Maintenance Units -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">{{ $property_performance['maintenance_apartments'] }}</h4>
                        <p class="text-sm text-gray-500">Under Maintenance</p>
                    </div>
                </div>

                <!-- Top Performing Apartments -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Apartments</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apartment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Annual Revenue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payments</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Occupancy</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($property_performance['apartment_performance']->take(5) as $performance)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $performance['apartment']->full_address }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        LKR {{ number_format($performance['annual_revenue'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $performance['payment_count'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $performance['occupancy_months'] }} months
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Combined Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Owner Analytics -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Owner Analytics
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <p class="text-2xl font-bold text-blue-600">{{ $owner_analytics['total_owners'] }}</p>
                            <p class="text-sm text-gray-600">Active Owners</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <p class="text-2xl font-bold text-green-600">{{ $owner_analytics['retention_rate'] }}%</p>
                            <p class="text-sm text-gray-600">Retention Rate</p>
                        </div>
                    </div>

                    <!-- Payment Behavior -->
                    <div class="mb-6">
                        <h5 class="font-semibold text-gray-900 mb-3">Payment Behavior</h5>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Always On Time</span>
                                <span class="font-medium text-green-600">{{ $owner_analytics['payment_behavior']['always_on_time'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Usually Late</span>
                                <span class="font-medium text-yellow-600">{{ $owner_analytics['payment_behavior']['usually_late'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Frequent Defaulters</span>
                                <span class="font-medium text-red-600">{{ $owner_analytics['payment_behavior']['frequent_defaulters'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Satisfaction Score -->
                    <div>
                        <h5 class="font-semibold text-gray-900 mb-3">Satisfaction Score</h5>
                        <div class="flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-3 mr-3">
                                <div class="bg-blue-600 h-3 rounded-full" style="width: {{ ($owner_analytics['owner_satisfaction']->avg_satisfaction / 5) * 100 }}%"></div>
                            </div>
                            <span class="text-sm font-medium">{{ number_format($owner_analytics['owner_satisfaction']->avg_satisfaction, 1) }}/5.0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Analytics -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Maintenance Analytics
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="text-center p-4 bg-orange-50 rounded-lg">
                            <p class="text-2xl font-bold text-orange-600">{{ $maintenance_analytics['current_month_requests'] }}</p>
                            <p class="text-sm text-gray-600">This Month</p>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <p class="text-2xl font-bold text-blue-600">{{ $maintenance_analytics['response_time_stats']['average_response_time'] }}</p>
                            <p class="text-sm text-gray-600">Avg Response (days)</p>
                        </div>
                    </div>

                    <!-- Status Breakdown -->
                    <div class="mb-6">
                        <h5 class="font-semibold text-gray-900 mb-3">Request Status</h5>
                        <div class="space-y-2">
                            @foreach($maintenance_analytics['status_breakdown'] as $status => $count)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                <span class="font-medium 
                                    @if($status === 'pending') text-yellow-600
                                    @elseif($status === 'in_progress') text-blue-600
                                    @elseif($status === 'completed') text-green-600
                                    @else text-gray-600 @endif
                                ">{{ $count }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Cost Analysis -->
                    @if($maintenance_analytics['cost_analysis'])
                    <div>
                        <h5 class="font-semibold text-gray-900 mb-3">Cost Analysis</h5>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-lg font-bold text-gray-900">LKR {{ number_format($maintenance_analytics['cost_analysis']->avg_cost, 2) }}</p>
                            <p class="text-sm text-gray-600">Average Cost</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Monthly Trends Chart -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">12-Month Trends</h3>
            </div>
            <div class="p-6">
                <div class="h-96">
                    <canvas id="monthlyTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Trend Chart
            const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
            const monthlyData = @json($monthly_trends);
            
            new Chart(revenueTrendCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => item.month),
                    datasets: [{
                        label: 'Revenue',
                        data: monthlyData.map(item => item.revenue),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'LKR ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Payment Methods Chart
            const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
            const paymentMethods = @json($financial_overview['payment_methods']);
            
            new Chart(paymentMethodsCtx, {
                type: 'doughnut',
                data: {
                    labels: paymentMethods.map(item => item.payment_method),
                    datasets: [{
                        data: paymentMethods.map(item => item.total),
                        backgroundColor: [
                            '#3B82F6',
                            '#10B981',
                            '#F59E0B',
                            '#EF4444',
                            '#8B5CF6'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Monthly Trends Chart
            const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
            
            new Chart(monthlyTrendsCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => item.month),
                    datasets: [
                        {
                            label: 'Revenue',
                            data: monthlyData.map(item => item.revenue),
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            yAxisID: 'y'
                        },
                        {
                            label: 'Expenses',
                            data: monthlyData.map(item => item.expenses),
                            borderColor: '#EF4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            yAxisID: 'y'
                        },
                        {
                            label: 'Maintenance Requests',
                            data: monthlyData.map(item => item.maintenance_requests),
                            borderColor: '#F59E0B',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            ticks: {
                                callback: function(value) {
                                    return 'LKR ' + value.toLocaleString();
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    }
                }
            });
        });

        // Export Dashboard Function
        function exportDashboard() {
            // Show loading message
            alert('Generating analytics report... This feature will be implemented soon.');
        }
    </script>
    @endpush
</x-app-layout>
