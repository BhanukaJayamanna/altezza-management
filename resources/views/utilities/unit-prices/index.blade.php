<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Utility Unit Prices') }}
            </h2>
            <a href="{{ route('utility-unit-prices.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                Add Unit Price
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            
            <!-- Filters -->
            <x-card class="mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('utility-unit-prices.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status
                                </label>
                                <select id="status" name="status" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <!-- Date Filter -->
                            <div>
                                <label for="effective_from" class="block text-sm font-medium text-gray-700 mb-2">
                                    Effective From
                                </label>
                                <input type="date" id="effective_from" name="effective_from" value="{{ request('effective_from') }}" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <div class="flex space-x-2">
                                <x-button type="submit">
                                    Apply Filters
                                </x-button>
                                <a href="{{ route('utility-unit-prices.index') }}" 
                                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                    Clear Filters
                                </a>
                            </div>
                            
                            <div class="flex space-x-2">
                                <a href="{{ route('utility-unit-prices.current') }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Current Prices
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </x-card>

            <!-- Unit Prices Table -->
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Utility Unit Prices</h3>
                        <div class="text-sm text-gray-500">
                            Showing {{ $unitPrices->firstItem() ?? 0 }} to {{ $unitPrices->lastItem() ?? 0 }} of {{ $unitPrices->total() }} results
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden">
                    @if($unitPrices->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'type', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="group inline-flex items-center hover:text-gray-700">
                                        Utility Type
                                        <svg class="ml-1 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                        </svg>
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price per Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective From</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Until</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($unitPrices as $unitPrice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $unitPrice->type === 'electricity' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($unitPrice->type === 'water' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                        {{ ucfirst($unitPrice->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-medium text-lg">@currency($unitPrice->price_per_unit, 4)</span>
                                    <div class="text-xs text-gray-500">per unit</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $unitPrice->effective_from->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $unitPrice->effective_until ? $unitPrice->effective_until->format('M j, Y') : 'Ongoing' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $unitPrice->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($unitPrice->status) }}
                                    </span>
                                    @if($unitPrice->isCurrent())
                                    <div class="text-xs text-green-600 mt-1 font-medium">
                                        Current
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $unitPrice->created_at->format('M j, Y') }}
                                    <div class="text-xs text-gray-500">
                                        {{ $unitPrice->created_at->format('g:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('utility-unit-prices.show', $unitPrice) }}" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        @can('admin')
                                        @if($unitPrice->status === 'active')
                                        <a href="{{ route('utility-unit-prices.edit', $unitPrice) }}" 
                                           class="text-blue-600 hover:text-blue-900">Edit</a>
                                        @endif
                                        <form method="POST" action="{{ route('utility-unit-prices.destroy', $unitPrice) }}" 
                                              onsubmit="return confirm('Are you sure you want to delete this unit price?')" class="inline">
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
                        {{ $unitPrices->appends(request()->query())->links() }}
                    </div>
                    @else
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No unit prices found</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if(request()->hasAny(['type', 'status', 'effective_from']))
                                No unit prices match your current filter criteria.
                            @else
                                Get started by adding your first utility unit price.
                            @endif
                        </p>
                        <div class="mt-6">
                            @if(request()->hasAny(['type', 'status', 'effective_from']))
                                <a href="{{ route('utility-unit-prices.index') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                    Clear Filters
                                </a>
                            @else
                                <a href="{{ route('utility-unit-prices.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Add First Unit Price
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
