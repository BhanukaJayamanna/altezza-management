<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Unit Price') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('utility-unit-prices.show', $unitPrice) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    View Details
                </a>
                <a href="{{ route('utility-unit-prices.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Unit Prices
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Edit Unit Price Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Modify pricing for utility consumption per unit.</p>
                </div>

                <form method="POST" action="{{ route('utility-unit-prices.update', $unitPrice) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Utility Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Utility Type <span class="text-red-500">*</span>
                            </label>
                            <select id="type" name="type" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Type</option>
                                <option value="electricity" {{ old('type', $unitPrice->type) == 'electricity' ? 'selected' : '' }}>Electricity (per kWh)</option>
                                <option value="water" {{ old('type', $unitPrice->type) == 'water' ? 'selected' : '' }}>Water (per gallon)</option>
                                <option value="gas" {{ old('type', $unitPrice->type) == 'gas' ? 'selected' : '' }}>Gas (per cubic foot)</option>
                            </select>
                        </div>

                        <!-- Price Per Unit -->
                        <div>
                            <label for="price_per_unit" class="block text-sm font-medium text-gray-700 mb-2">
                                Price Per Unit ($) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" id="price_per_unit" name="price_per_unit" value="{{ old('price_per_unit', $unitPrice->price_per_unit) }}" 
                                       step="0.0001" min="0" required
                                       class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0.0000">
                            </div>
                        </div>

                        <!-- Effective From -->
                        <div>
                            <label for="effective_from" class="block text-sm font-medium text-gray-700 mb-2">
                                Effective From <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="effective_from" name="effective_from" value="{{ old('effective_from', $unitPrice->effective_from ? $unitPrice->effective_from->format('Y-m-d') : '') }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Effective To -->
                        <div>
                            <label for="effective_to" class="block text-sm font-medium text-gray-700 mb-2">
                                Effective Until (Optional)
                            </label>
                            <input type="date" id="effective_to" name="effective_to" value="{{ old('effective_to', $unitPrice->effective_to ? $unitPrice->effective_to->format('Y-m-d') : '') }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-sm text-gray-500">Leave empty for ongoing price</p>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $unitPrice->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Active (enable this price immediately)
                        </label>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description (Optional)
                        </label>
                        <textarea id="description" name="description" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Optional notes about this price change...">{{ old('description', $unitPrice->description) }}</textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Changes will affect future utility bills
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('utility-unit-prices.show', $unitPrice) }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Unit Price
                            </button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
