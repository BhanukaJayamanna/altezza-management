<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                    {{ __('Management Fee Dashboard') }}
                </h2>
                <p class="text-slate-600 text-sm mt-1">Manage quarterly management fees and sinking fund calculations</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('management-fees.settings') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-cog mr-2"></i>Settings
                </a>
                <a href="{{ route('management-fees.quarterly-invoices') }}" 
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-file-invoice mr-2"></i>Quarterly Invoices
                </a>
                <a href="{{ route('management-fees.analytics') }}" 
                   class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-chart-bar mr-2"></i>Analytics
                </a>
            </div>
        </div>
    </x-slot>

<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Apartments -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                        <i class="fas fa-building text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Apartments</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_apartments'] }}</div>
                </div>
            </div>
        </div>

        <!-- Total Area -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white">
                        <i class="fas fa-expand-arrows-alt text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Area</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_area_sqft'], 0) }} sqft</div>
                </div>
            </div>
        </div>

        <!-- Quarterly Management Fees -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-yellow-500 text-white">
                        <i class="fas fa-coins text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Quarterly Management</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $settings['currency_symbol'] }}{{ number_format($stats['quarterly_totals']['management_fees'], 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Quarterly Sinking Fund -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white">
                        <i class="fas fa-piggy-bank text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Quarterly Sinking Fund</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $settings['currency_symbol'] }}{{ number_format($stats['quarterly_totals']['sinking_funds'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Quarter Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Current Quarter Summary -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ $currentQuarterStats['quarter_name'] }} Summary</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Invoices</span>
                        <span class="font-semibold text-gray-900">{{ $currentQuarterStats['totals']['invoices'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Amount</span>
                        <span class="font-semibold text-gray-900">{{ $settings['currency_symbol'] }}{{ number_format($currentQuarterStats['totals']['net_amount'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Collected</span>
                        <span class="font-semibold text-green-600">{{ $settings['currency_symbol'] }}{{ number_format($currentQuarterStats['financial']['collected'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Outstanding</span>
                        <span class="font-semibold text-red-600">{{ $settings['currency_symbol'] }}{{ number_format($currentQuarterStats['financial']['outstanding'], 2) }}</span>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Collection Rate</span>
                            <span class="font-semibold text-blue-600">{{ $currentQuarterStats['financial']['collection_rate'] }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Ratios -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Current Fee Ratios</h3>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Management Fee Ratio</span>
                            <span class="font-bold text-blue-600">{{ number_format($settings['management_fee_ratio'], 2) }} per sqft</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            Applied monthly, multiplied by 3 for quarterly billing
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Sinking Fund Ratio</span>
                            <span class="font-bold text-green-600">{{ number_format($settings['sinking_fund_ratio'], 2) }} per sqft</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            Applied monthly, multiplied by 3 for quarterly billing
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Total Combined Ratio</span>
                            <span class="font-bold text-purple-600">{{ number_format($settings['management_fee_ratio'] + $settings['sinking_fund_ratio'], 2) }} per sqft</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            Per month (x3 for quarterly total)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Recent Management Fee Invoices</h3>
                <a href="{{ route('management-fees.quarterly-invoices') }}" 
                   class="text-blue-500 hover:text-blue-600 text-sm font-medium">
                    View All →
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apartment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentInvoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                            <a href="{{ route('management-fees.show-invoice', $invoice->id) }}" class="hover:underline">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>
                                <div class="font-medium">{{ $invoice->apartment->number }}</div>
                                @if($invoice->apartment->assessment_no)
                                <div class="text-gray-500">Assessment: {{ $invoice->apartment->assessment_no }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $invoice->quarter_name }} {{ $invoice->year }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $settings['currency_symbol'] }}{{ number_format($invoice->net_total, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($invoice->status === 'paid') bg-green-100 text-green-800
                                @elseif($invoice->status === 'overdue') bg-red-100 text-red-800
                                @elseif($invoice->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $invoice->due_date->format('M j, Y') }}
                            @if($invoice->isOverdue())
                                <div class="text-red-500 text-xs">{{ $invoice->days_overdue }} days overdue</div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No management fee invoices found.
                            <a href="{{ route('management-fees.quarterly-invoices') }}" class="text-blue-500 hover:underline ml-1">
                                Generate quarterly invoices
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-8 flex justify-center space-x-4">
        <a href="{{ route('management-fees.quarterly-invoices', ['quarter' => \App\Models\ManagementFeeInvoice::getCurrentQuarter(), 'year' => now()->year]) }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition duration-200">
            <i class="fas fa-plus mr-2"></i>Generate Current Quarter Invoices
        </a>
        
        <form method="POST" action="{{ route('management-fees.process-overdue') }}" class="inline">
            @csrf
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg transition duration-200">
                <i class="fas fa-exclamation-triangle mr-2"></i>Process Overdue Invoices
            </button>
        </form>
    </div>
    </div>
</div>
</x-app-layout>
