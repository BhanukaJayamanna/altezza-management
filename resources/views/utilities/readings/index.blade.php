<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Utility Readings') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('utility-readings.bulk') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    Bulk Entry
                </a>
                <a href="{{ route('utility-readings.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Add Reading
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            
            <!-- Filters -->
            <x-card class="mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('utility-readings.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Meter Filter -->
                            <div>
                                <label for="meter_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Meter
                                </label>
                                <select id="meter_id" name="meter_id" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Meters</option>
                                    @foreach($meters as $meter)
                                        <option value="{{ $meter->id }}" {{ request('meter_id') == $meter->id ? 'selected' : '' }}>
                                            {{ $meter->apartment->number }} - {{ ucfirst($meter->type) }} ({{ $meter->meter_number }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Utility Type Filter -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Utility Type
                                </label>
                                <select id="type" name="type" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Types</option>
                                    <option value="electricity" {{ request('type') == 'electricity' ? 'selected' : '' }}>Electricity</option>
                                    <option value="water" {{ request('type') == 'water' ? 'selected' : '' }}>Water</option>
                                    <option value="gas" {{ request('type') == 'gas' ? 'selected' : '' }}>Gas</option>
                                </select>
                            </div>

                            <!-- Date Range -->
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                                    From Date
                                </label>
                                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

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
                                <a href="{{ route('utility-readings.index') }}" 
                                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                    Clear Filters
                                </a>
                            </div>
                            
                            <div class="flex space-x-2">
                                <a href="{{ route('utility-readings.export', request()->query()) }}" 
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

            <!-- Readings Table -->
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Utility Readings</h3>
                        <div class="text-sm text-gray-500">
                            Showing {{ $readings->firstItem() ?? 0 }} to {{ $readings->lastItem() ?? 0 }} of {{ $readings->total() }} results
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden">
                    @if($readings->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'reading_date', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="group inline-flex items-center hover:text-gray-700">
                                        Date
                                        <svg class="ml-1 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                        </svg>
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apartment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utility</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reading</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recorded By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($readings as $reading)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reading->reading_date->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <a href="{{ route('apartments.show', $reading->meter->apartment) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        {{ $reading->meter->apartment->number }}
                                    </a>
                                    <div class="text-xs text-gray-500">{{ $reading->meter->apartment->block }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $reading->meter->type === 'electricity' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($reading->meter->type === 'water' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                        {{ ucfirst($reading->meter->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                    <a href="{{ route('utility-meters.show', $reading->meter) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        {{ $reading->meter->meter_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                    {{ number_format($reading->reading, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reading->usage ? number_format($reading->usage, 2) : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-medium">@currency($reading->amount)</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reading->recordedBy->name }}
                                    <div class="text-xs text-gray-500">
                                        {{ $reading->created_at->format('M j, g:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('utility-readings.show', $reading) }}" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        @can('admin')
                                        <a href="{{ route('utility-readings.edit', $reading) }}" 
                                           class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <form method="POST" action="{{ route('utility-readings.destroy', $reading) }}" 
                                              onsubmit="return confirm('Are you sure you want to delete this reading?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $readings->appends(request()->query())->links() }}
                    </div>
                    @else
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No readings found</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if(request()->hasAny(['meter_id', 'type', 'date_from', 'date_to']))
                                No readings match your current filter criteria.
                            @else
                                Get started by adding your first utility reading.
                            @endif
                        </p>
                        <div class="mt-6">
                            @if(request()->hasAny(['meter_id', 'type', 'date_from', 'date_to']))
                                <a href="{{ route('utility-readings.index') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                    Clear Filters
                                </a>
                            @else
                                <a href="{{ route('utility-readings.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Add First Reading
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
