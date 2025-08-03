<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Unit Price Details') }}
            </h2>
            <div class="flex space-x-2">
                @can('admin')
                @if($unitPrice->status === 'active')
                <a href="{{ route('utility-unit-prices.edit', $unitPrice) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Edit Price
                </a>
                @endif
                @endcan
                <a href="{{ route('utility-unit-prices.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Unit Prices
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Unit Price Information Card -->
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Unit Price Information</h3>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $unitPrice->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($unitPrice->status) }}
                            </span>
                            @if($unitPrice->isCurrent())
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                Current Price
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Utility Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $unitPrice->type === 'electricity' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($unitPrice->type === 'water' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ ucfirst($unitPrice->type) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Price Per Unit</dt>
                            <dd class="mt-1 text-2xl font-bold text-gray-900">
                                @currency($unitPrice->price_per_unit, 4)
                                <span class="text-sm font-normal text-gray-500">
                                    per {{ $unitPrice->type === 'electricity' ? 'kWh' : ($unitPrice->type === 'water' ? 'gallon' : 'cubic foot') }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Effective Period</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <div>From: {{ $unitPrice->effective_from ? $unitPrice->effective_from->format('M j, Y') : 'Not set' }}</div>
                                <div>Until: {{ $unitPrice->effective_to ? $unitPrice->effective_to->format('M j, Y') : 'Ongoing' }}</div>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $unitPrice->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($unitPrice->status) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $unitPrice->created_at ? $unitPrice->created_at->format('M j, Y g:i A') : 'Not available' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $unitPrice->updated_at ? $unitPrice->updated_at->format('M j, Y g:i A') : 'Not available' }}</dd>
                        </div>
                    </dl>

                    @if($unitPrice->description)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Description</dt>
                        <dd class="text-sm text-gray-900 bg-gray-50 p-4 rounded-md">{{ $unitPrice->description }}</dd>
                    </div>
                    @endif
                </div>
            </x-card>

            <!-- Usage Statistics -->
            @if($unitPrice->isCurrent())
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Current Usage Statistics</h3>
                    <p class="mt-1 text-sm text-gray-600">Statistics for meters using this utility type</p>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div class="bg-gray-50 px-4 py-5 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Meters</dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                {{ \App\Models\UtilityMeter::where('type', $unitPrice->type)->where('status', 'active')->count() }}
                            </dd>
                        </div>

                        <div class="bg-blue-50 px-4 py-5 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 truncate">This Month Usage</dt>
                            <dd class="mt-1 text-3xl font-semibold text-blue-600">
                                {{ number_format(\App\Models\UtilityReading::whereHas('meter', function($q) use ($unitPrice) {
                                    $q->where('type', $unitPrice->type);
                                })->whereMonth('reading_date', now()->month)->sum('usage'), 2) }}
                            </dd>
                        </div>

                        <div class="bg-green-50 px-4 py-5 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 truncate">Revenue This Month</dt>
                            <dd class="mt-1 text-3xl font-semibold text-green-600">
                                @currency(\App\Models\UtilityReading::whereHas('meter', function($q) use ($unitPrice) {
                                    $q->where('type', $unitPrice->type);
                                })->whereMonth('reading_date', now()->month)->sum('amount'))
                            </dd>
                        </div>
                    </dl>
                </div>
            </x-card>
            @endif

            <!-- Price History -->
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Price History for {{ ucfirst($unitPrice->type) }}</h3>
                    <p class="mt-1 text-sm text-gray-600">Historical pricing for this utility type</p>
                </div>

                <div class="overflow-hidden">
                    @php
                        $priceHistory = \App\Models\UtilityUnitPrice::where('type', $unitPrice->type)
                            ->orderBy('effective_from', 'desc')
                            ->take(10)
                            ->get();
                    @endphp

                    @if($priceHistory->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective From</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Until</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($priceHistory as $price)
                            <tr class="{{ $price->id === $unitPrice->id ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    @currency($price->price_per_unit, 4)
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $price->effective_from ? $price->effective_from->format('M j, Y') : 'Not set' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $price->effective_to ? $price->effective_to->format('M j, Y') : 'Ongoing' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $price->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($price->status) }}
                                    </span>
                                    @if($price->isCurrent())
                                    <div class="text-xs text-blue-600 mt-1">Current</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($price->id !== $unitPrice->id)
                                        <a href="{{ route('utility-unit-prices.show', $price) }}" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                    @else
                                        <span class="text-blue-600 font-medium">Current</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
