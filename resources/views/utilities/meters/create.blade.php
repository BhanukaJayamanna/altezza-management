<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Utility Meter') }}
            </h2>
            <a href="{{ route('utility-meters.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Meters
            </a>
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
                    <p class="mt-1 text-sm text-gray-600">Create a new utility meter for an apartment.</p>
                </div>

                <form method="POST" action="{{ route('utility-meters.store') }}" class="p-6 space-y-6">
                    @csrf

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
                                    <option value="{{ $apartment->id }}" {{ old('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                        {{ $apartment->number }} - {{ $apartment->type }} ({{ $apartment->assessment_no }})
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
                                <option value="electricity" {{ old('type') == 'electricity' ? 'selected' : '' }}>Electricity</option>
                                <option value="water" {{ old('type') == 'water' ? 'selected' : '' }}>Water</option>
                                <option value="gas" {{ old('type') == 'gas' ? 'selected' : '' }}>Gas</option>
                            </select>
                        </div>

                        <!-- Meter Number -->
                        <div>
                            <label for="meter_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Meter Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="meter_number" name="meter_number" value="{{ old('meter_number') }}" required
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
                                <input type="number" id="rate_per_unit" name="rate_per_unit" value="{{ old('rate_per_unit') }}" 
                                       step="0.0001" min="0" required
                                       class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0.0000">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Default rate per unit (can be overridden by unit prices)</p>
                        </div>

                        <!-- Last Reading (Optional) -->
                        <div>
                            <label for="last_reading" class="block text-sm font-medium text-gray-700 mb-2">
                                Initial Reading (Optional)
                            </label>
                            <input type="number" id="last_reading" name="last_reading" value="{{ old('last_reading') }}" 
                                   step="0.01" min="0"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0.00">
                            <p class="mt-1 text-sm text-gray-500">Enter current meter reading if available</p>
                        </div>

                        <!-- Last Reading Date -->
                        <div>
                            <label for="last_reading_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Reading Date (Optional)
                            </label>
                            <input type="date" id="last_reading_date" name="last_reading_date" value="{{ old('last_reading_date') }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select id="status" name="status" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="faulty" {{ old('status') == 'faulty' ? 'selected' : '' }}>Faulty</option>
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
                                  placeholder="Any additional notes about this meter...">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('utility-meters.index') }}" 
                           class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <x-button type="submit">
                            Create Meter
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
