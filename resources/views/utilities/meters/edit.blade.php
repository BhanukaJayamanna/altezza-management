<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Utility Meter') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('utility-meters.show', $meter) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    View Meter
                </a>
                <a href="{{ route('utility-meters.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Meters
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
                    <h3 class="text-lg font-medium text-gray-900">Meter Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Update meter details and configuration.</p>
                </div>

                <form method="POST" action="{{ route('utility-meters.update', $meter) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Apartment -->
                        <div>
                            <label for="apartment_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Apartment <span class="text-red-500">*</span>
                            </label>
                            <select id="apartment_id" name="apartment_id" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Apartment</option>
                                @foreach($apartments as $apartment)
                                    <option value="{{ $apartment->id }}" {{ (old('apartment_id', $meter->apartment_id) == $apartment->id) ? 'selected' : '' }}>
                                        {{ $apartment->number }} - {{ $apartment->type }} ({{ $apartment->block }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Utility Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Utility Type <span class="text-red-500">*</span>
                            </label>
                            <select id="type" name="type" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Type</option>
                                <option value="electricity" {{ old('type', $meter->type) == 'electricity' ? 'selected' : '' }}>Electricity</option>
                                <option value="water" {{ old('type', $meter->type) == 'water' ? 'selected' : '' }}>Water</option>
                                <option value="gas" {{ old('type', $meter->type) == 'gas' ? 'selected' : '' }}>Gas</option>
                            </select>
                        </div>

                        <!-- Meter Number -->
                        <div>
                            <label for="meter_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Meter Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="meter_number" name="meter_number" value="{{ old('meter_number', $meter->meter_number) }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter unique meter number">
                        </div>

                        <!-- Rate Per Unit -->
                        <div>
                            <label for="rate_per_unit" class="block text-sm font-medium text-gray-700 mb-2">
                                Rate Per Unit ($) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" id="rate_per_unit" name="rate_per_unit" value="{{ old('rate_per_unit', $meter->rate_per_unit) }}" 
                                       step="0.0001" min="0" required
                                       class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0.0000">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Default rate per unit (can be overridden by unit prices)</p>
                        </div>

                        <!-- Last Reading -->
                        <div>
                            <label for="last_reading" class="block text-sm font-medium text-gray-700 mb-2">
                                Last Reading
                            </label>
                            <input type="number" id="last_reading" name="last_reading" value="{{ old('last_reading', $meter->last_reading) }}" 
                                   step="0.01" min="0"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0.00">
                            <p class="mt-1 text-sm text-gray-500">Current meter reading</p>
                        </div>

                        <!-- Last Reading Date -->
                        <div>
                            <label for="last_reading_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Last Reading Date
                            </label>
                            <input type="date" id="last_reading_date" name="last_reading_date" 
                                   value="{{ old('last_reading_date', $meter->last_reading_date?->format('Y-m-d')) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select id="status" name="status" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="active" {{ old('status', $meter->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $meter->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="faulty" {{ old('status', $meter->status) == 'faulty' ? 'selected' : '' }}>Faulty</option>
                            </select>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any additional notes about this meter...">{{ old('notes', $meter->notes) }}</textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('utility-meters.show', $meter) }}" 
                           class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <x-button type="submit">
                            Update Meter
                        </x-button>
                    </div>
                </form>

                <!-- Danger Zone -->
                @can('admin')
                <div class="mt-8 border-t border-red-200 pt-6">
                    <div class="bg-red-50 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Danger Zone
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>Deleting this meter will remove all associated readings and billing history. This action cannot be undone.</p>
                                </div>
                                <div class="mt-4">
                                    <form method="POST" action="{{ route('utility-meters.destroy', $meter) }}" 
                                          onsubmit="return confirm('Are you sure you want to delete this meter? This will also delete all readings and billing history.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                            Delete Meter
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
            </x-card>
        </div>
    </div>
</x-app-layout>
