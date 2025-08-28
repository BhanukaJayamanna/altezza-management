<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                    {{ __('Management Fee Analytics') }}
                </h2>
                <p class="text-slate-600 text-sm mt-1">Comprehensive analytics and reporting for {{ $currentYear }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('management-fees.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <a href="{{ route('management-fees.export-quarterly') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-download mr-2"></i>Export Data
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Year Selection -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Year Selection</h3>
                    <form method="GET" class="flex items-center space-x-4">
                        <select name="year" onchange="this.form.submit()" 
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @for ($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </form>
                </div>
            </div>

            <!-- Overall Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-500 bg-opacity-75">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Apartments</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $totalStats['total_apartments'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500 bg-opacity-75">
                            <i class="fas fa-expand-arrows-alt text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Area</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalStats['total_area_sqft'] ?? 0, 0) }} sqft</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-500 bg-opacity-75">
                            <i class="fas fa-coins text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Annual Management Fees</p>
                            <p class="text-2xl font-semibold text-gray-900">Rs. {{ number_format(($totalStats['quarterly_totals']['management_fees'] ?? 0) * 4, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-500 bg-opacity-75">
                            <i class="fas fa-piggy-bank text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Annual Sinking Fund</p>
                            <p class="text-2xl font-semibold text-gray-900">Rs. {{ number_format(($totalStats['quarterly_totals']['sinking_funds'] ?? 0) * 4, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quarterly Breakdown -->
            <div class="bg-white rounded-lg shadow-md mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quarterly Breakdown for {{ $currentYear }}</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($quarters as $quarter => $stats)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-lg font-semibold text-gray-900">Q{{ $quarter }}</h4>
                                <div class="flex items-center space-x-2">
                                    @if(($stats['total_invoices'] ?? 0) > 0)
                                        <span class="text-green-600">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    @else
                                        <span class="text-gray-400">
                                            <i class="fas fa-circle"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Invoices:</span>
                                    <span class="font-medium">{{ $stats['total_invoices'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Paid:</span>
                                    <span class="font-medium text-green-600">{{ $stats['paid_invoices'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Pending:</span>
                                    <span class="font-medium text-yellow-600">{{ $stats['pending_invoices'] ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Overdue:</span>
                                    <span class="font-medium text-red-600">{{ $stats['overdue_invoices'] ?? 0 }}</span>
                                </div>
                                <div class="border-t pt-2">
                                    <div class="flex justify-between text-sm font-semibold">
                                        <span>Total Amount:</span>
                                        <span>Rs. {{ number_format($stats['total_amount'] ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ route('management-fees.quarterly-invoices', ['quarter' => $quarter, 'year' => $currentYear]) }}" 
                                   class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-md text-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Collection Rate Chart -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Collection Rate by Quarter</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($quarters as $quarter => $stats)
                            @php
                                $collectionRate = ($stats['total_invoices'] ?? 0) > 0 
                                    ? (($stats['paid_invoices'] ?? 0) / $stats['total_invoices']) * 100 
                                    : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span>Q{{ $quarter }} {{ $currentYear }}</span>
                                    <span>{{ number_format($collectionRate, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $collectionRate }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Outstanding Analysis -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Outstanding Analysis</h3>
                    </div>
                    <div class="p-6">
                        @php
                            $totalOutstanding = collect($quarters)->sum(function($stats) {
                                return ($stats['total_amount'] ?? 0) - ($stats['paid_amount'] ?? 0);
                            });
                            $totalInvoiced = collect($quarters)->sum('total_amount');
                            $outstandingRate = $totalInvoiced > 0 ? ($totalOutstanding / $totalInvoiced) * 100 : 0;
                        @endphp
                        
                        <div class="text-center mb-6">
                            <div class="text-3xl font-bold text-red-600 mb-2">
                                Rs. {{ number_format($totalOutstanding, 2) }}
                            </div>
                            <div class="text-gray-500">Total Outstanding</div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Outstanding Rate:</span>
                                <span class="font-semibold">{{ number_format($outstandingRate, 1) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Total Invoiced:</span>
                                <span class="font-semibold">Rs. {{ number_format($totalInvoiced, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Total Collected:</span>
                                <span class="font-semibold text-green-600">Rs. {{ number_format($totalInvoiced - $totalOutstanding, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Apartments by Outstanding -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Top Apartments by Outstanding Amount</h3>
                </div>
                <div class="p-6">
                    @php
                        // Mock data - replace with actual query
                        $topOutstanding = collect([
                            ['apartment' => '202/1/1/A', 'owner' => 'John Doe', 'amount' => 125000],
                            ['apartment' => '202/1/2/B', 'owner' => 'Jane Smith', 'amount' => 98500],
                            ['apartment' => '202/1/3/C', 'owner' => 'Bob Wilson', 'amount' => 87200],
                            ['apartment' => '202/1/4/D', 'owner' => 'Alice Brown', 'amount' => 76800],
                            ['apartment' => '202/1/5/E', 'owner' => 'Charlie Davis', 'amount' => 65400],
                        ]);
                    @endphp
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Apartment</th>
                                    <th class="text-left py-2">Owner</th>
                                    <th class="text-right py-2">Outstanding Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topOutstanding as $item)
                                <tr class="border-b">
                                    <td class="py-2 font-medium">{{ $item['apartment'] }}</td>
                                    <td class="py-2">{{ $item['owner'] }}</td>
                                    <td class="py-2 text-right font-semibold text-red-600">
                                        Rs. {{ number_format($item['amount'], 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
