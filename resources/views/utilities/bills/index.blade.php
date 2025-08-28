<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Utility Bills') }}
            </h2>
            <div class="flex space-x-2">
                @can('admin')
                <a href="{{ route('utility-bills.generate') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    Generate Bills
                </a>
                @endcan
                <a href="{{ route('utility-bills.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Create Bill
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <x-card>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Bills</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_bills'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Paid Bills</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['paid_bills'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Bills</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_bills'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Overdue Bills</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['overdue_bills'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Filters -->
            <x-card class="mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('utility-bills.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- Apartment Filter -->
                            <div>
                                <label for="apartment_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Apartment
                                </label>
                                <select id="apartment_id" name="apartment_id" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Apartments</option>
                                    @foreach($apartments as $apartment)
                                        <option value="{{ $apartment->id }}" {{ request('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                            {{ $apartment->number }} ({{ $apartment->assessment_no }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status
                                </label>
                                <select id="status" name="status" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <!-- Period Filter -->
                            <div>
                                <label for="period" class="block text-sm font-medium text-gray-700 mb-2">
                                    Billing Period
                                </label>
                                <input type="month" id="period" name="period" value="{{ request('period') }}" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Date From -->
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                                    From Date
                                </label>
                                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Date To -->
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                                    To Date
                                </label>
                                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <div class="flex space-x-2">
                                <x-button type="submit">
                                    Apply Filters
                                </x-button>
                                <a href="{{ route('utility-bills.index') }}" 
                                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                    Clear Filters
                                </a>
                            </div>
                            
                            <div class="flex space-x-2">
                                <a href="{{ route('utility-bills.export', request()->query()) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </x-card>

            <!-- Bills Table -->
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Utility Bills</h3>
                        <div class="text-sm text-gray-500">
                            Showing {{ $bills->firstItem() ?? 0 }} to {{ $bills->lastItem() ?? 0 }} of {{ $bills->total() }} results
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden">
                    @if($bills->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'bill_number', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="group inline-flex items-center hover:text-gray-700">
                                        Bill #
                                        <svg class="ml-1 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                        </svg>
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apartment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bills as $bill)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <a href="{{ route('utility-bills.show', $bill) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        {{ $bill->bill_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <a href="{{ route('apartments.show', $bill->apartment) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        {{ $bill->apartment->number }}
                                    </a>
                                    <div class="text-xs text-gray-500">{{ $bill->apartment->assessment_no }}</div>
                                    @if($bill->apartment->currentLease && $bill->apartment->currentLease->owner)
                                    <div class="text-xs text-gray-500">{{ $bill->apartment->currentLease->owner->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::createFromFormat('m/Y', $bill->period)->format('M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-medium text-lg">@currency($bill->total_amount)</span>
                                    @if($bill->paid_amount > 0)
                                    <div class="text-xs text-green-600">
                                        Paid: @currency($bill->paid_amount)
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $bill->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                           ($bill->status === 'unpaid' ? 'bg-blue-100 text-blue-800' : 
                                           ($bill->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($bill->status) }}
                                    </span>
                                    @if($bill->isOverdue())
                                    <div class="text-xs text-red-600 mt-1">
                                        {{ $bill->daysOverdue() }} days overdue
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $bill->due_date->format('M j, Y') }}
                                    @if($bill->isOverdue())
                                    <div class="text-xs text-red-600">
                                        ({{ $bill->due_date->diffForHumans() }})
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('utility-bills.show', $bill) }}" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        @if($bill->status !== 'paid')
                                        <a href="{{ route('utility-bills.edit', $bill) }}" 
                                           class="text-blue-600 hover:text-blue-900">Edit</a>
                                        @endif
                                        <a href="{{ route('utility-bills.download-pdf', $bill) }}" 
                                           class="text-green-600 hover:text-green-900">PDF</a>
                                        @if($bill->status === 'unpaid' && auth()->user()->hasRole(['admin', 'manager']))
                                        <form method="POST" action="{{ route('utility-bills.mark-paid', $bill) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900">Mark Paid</button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $bills->appends(request()->query())->links() }}
                    </div>
                    @else
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No bills found</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if(request()->hasAny(['apartment_id', 'status', 'period', 'date_from', 'date_to']))
                                No bills match your current filter criteria.
                            @else
                                Get started by creating your first utility bill.
                            @endif
                        </p>
                        <div class="mt-6">
                            @if(request()->hasAny(['apartment_id', 'status', 'period', 'date_from', 'date_to']))
                                <a href="{{ route('utility-bills.index') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                    Clear Filters
                                </a>
                            @else
                                <div class="flex justify-center space-x-4">
                                    @can('admin')
                                    <a href="{{ route('utility-bills.generate') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                        Generate Bills
                                    </a>
                                    @endcan
                                    <a href="{{ route('utility-bills.create') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Create Manual Bill
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
