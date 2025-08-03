<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Utility Reading') }}
            </h2>
            <a href="{{ route('utility-readings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Readings
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
                    <h3 class="text-lg font-medium text-gray-900">Reading Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Enter the meter reading details.</p>
                </div>

                <form method="POST" action="{{ route('utility-readings.store') }}" class="p-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Meter Selection -->
                        <div>
                            <label for="meter_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Meter <span class="text-red-500">*</span>
                            </label>
                            <select id="meter_id" name="meter_id" required onchange="updateMeterInfo()"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Meter</option>
                                @foreach($meters as $meter)
                                    <option value="{{ $meter->id }}" 
                                            data-type="{{ $meter->type }}"
                                            data-apartment="{{ $meter->apartment->number }}"
                                            data-last-reading="{{ $meter->last_reading }}"
                                            data-last-date="{{ $meter->last_reading_date?->format('Y-m-d') }}"
                                            {{ old('meter_id') == $meter->id ? 'selected' : '' }}>
                                        {{ $meter->apartment->number }} - {{ ucfirst($meter->type) }} ({{ $meter->meter_number }})
                                    </option>
                                @endforeach
                            </select>
                            <div id="meter-info" class="mt-1 text-sm text-gray-500"></div>
                        </div>

                        <!-- Reading Date -->
                        <div>
                            <label for="reading_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Reading Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="reading_date" name="reading_date" 
                                   value="{{ old('reading_date', now()->format('Y-m-d')) }}" 
                                   max="{{ now()->format('Y-m-d') }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Current Reading -->
                        <div>
                            <label for="current_reading" class="block text-sm font-medium text-gray-700 mb-2">
                                Current Reading <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="current_reading" name="current_reading" 
                                   value="{{ old('current_reading') }}" 
                                   step="0.01" min="0" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0.00">
                            <p class="mt-1 text-sm text-gray-500">Enter the current meter reading value</p>
                        </div>

                        <!-- Previous Reading (Display Only) -->
                        <div>
                            <label for="previous_reading_display" class="block text-sm font-medium text-gray-700 mb-2">
                                Previous Reading
                            </label>
                            <input type="text" id="previous_reading_display" readonly
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500"
                                   placeholder="Select meter to see previous reading">
                        </div>

                        <!-- Billing Period Start -->
                        <div>
                            <label for="billing_period_start" class="block text-sm font-medium text-gray-700 mb-2">
                                Billing Period Start <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="billing_period_start" name="billing_period_start" 
                                   value="{{ old('billing_period_start', now()->startOfMonth()->format('Y-m-d')) }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Billing Period End -->
                        <div>
                            <label for="billing_period_end" class="block text-sm font-medium text-gray-700 mb-2">
                                Billing Period End <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="billing_period_end" name="billing_period_end" 
                                   value="{{ old('billing_period_end', now()->endOfMonth()->format('Y-m-d')) }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Consumption Display -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Consumption Calculation</h4>
                        <div id="consumption-display" class="text-lg font-semibold text-blue-700">
                            Enter current reading to calculate consumption
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any additional notes about this reading...">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('utility-readings.index') }}" 
                           class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <x-button type="submit">
                            Add Reading
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateMeterInfo() {
            const select = document.getElementById('meter_id');
            const meterInfo = document.getElementById('meter-info');
            const previousReadingDisplay = document.getElementById('previous_reading_display');
            const consumptionDisplay = document.getElementById('consumption-display');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                const type = selectedOption.dataset.type;
                const apartment = selectedOption.dataset.apartment;
                const lastReading = selectedOption.dataset.lastReading;
                const lastDate = selectedOption.dataset.lastDate;
                
                meterInfo.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${type === 'electricity' ? 'yellow' : (type === 'water' ? 'blue' : 'orange')}-100 text-${type === 'electricity' ? 'yellow' : (type === 'water' ? 'blue' : 'orange')}-800">
                            ${type.charAt(0).toUpperCase() + type.slice(1)}
                        </span>
                        <span>Apartment ${apartment}</span>
                    </div>
                `;
                
                if (lastReading) {
                    previousReadingDisplay.value = `${parseFloat(lastReading).toFixed(2)} (${lastDate || 'Unknown date'})`;
                } else {
                    previousReadingDisplay.value = '0.00 (No previous reading)';
                }
                
                calculateConsumption();
            } else {
                meterInfo.textContent = '';
                previousReadingDisplay.value = '';
                consumptionDisplay.textContent = 'Enter current reading to calculate consumption';
            }
        }

        function calculateConsumption() {
            const select = document.getElementById('meter_id');
            const currentReading = parseFloat(document.getElementById('current_reading').value) || 0;
            const consumptionDisplay = document.getElementById('consumption-display');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value && currentReading > 0) {
                const previousReading = parseFloat(selectedOption.dataset.lastReading) || 0;
                const consumption = currentReading - previousReading;
                const type = selectedOption.dataset.type;
                const unit = type === 'electricity' ? 'kWh' : (type === 'water' ? 'gallons' : 'cubic feet');
                
                if (consumption >= 0) {
                    consumptionDisplay.innerHTML = `
                        <div class="flex items-center justify-between">
                            <span>Consumption: <strong>${consumption.toFixed(2)} ${unit}</strong></span>
                            <span class="text-sm">(${currentReading.toFixed(2)} - ${previousReading.toFixed(2)})</span>
                        </div>
                    `;
                } else {
                    consumptionDisplay.innerHTML = `
                        <div class="text-red-600">
                            <strong>Warning:</strong> Current reading is less than previous reading!
                        </div>
                    `;
                }
            } else {
                consumptionDisplay.textContent = 'Enter current reading to calculate consumption';
            }
        }

        // Event listeners
        document.getElementById('current_reading').addEventListener('input', calculateConsumption);
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateMeterInfo();
        });
    </script>
    @endpush
</x-app-layout>
