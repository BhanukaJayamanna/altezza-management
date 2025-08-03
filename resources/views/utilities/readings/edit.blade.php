<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Utility Reading') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('utility-readings.show', $reading) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    View Reading
                </a>
                <a href="{{ route('utility-readings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Readings
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
                    <h3 class="text-lg font-medium text-gray-900">Edit Reading Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Update the meter reading details.</p>
                </div>

                <form method="POST" action="{{ route('utility-readings.update', $reading) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Meter Information (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Meter
                            </label>
                            <div class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
                                {{ $reading->utilityMeter->apartment->number }} - {{ ucfirst($reading->utilityMeter->type) }} ({{ $reading->utilityMeter->meter_number }})
                            </div>
                            <div class="mt-1 text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $reading->utilityMeter->type === 'electricity' ? 'yellow' : ($reading->utilityMeter->type === 'water' ? 'blue' : 'orange') }}-100 text-{{ $reading->utilityMeter->type === 'electricity' ? 'yellow' : ($reading->utilityMeter->type === 'water' ? 'blue' : 'orange') }}-800">
                                    {{ ucfirst($reading->utilityMeter->type) }}
                                </span>
                                <span class="ml-2">Apartment {{ $reading->utilityMeter->apartment->number }}</span>
                            </div>
                        </div>

                        <!-- Reading Date -->
                        <div>
                            <label for="reading_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Reading Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="reading_date" name="reading_date" 
                                   value="{{ old('reading_date', $reading->reading_date->format('Y-m-d')) }}" 
                                   max="{{ now()->format('Y-m-d') }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Previous Reading (Display Only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Previous Reading
                            </label>
                            <input type="text" value="{{ number_format($reading->previous_reading, 2) }}" readonly
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500">
                        </div>

                        <!-- Current Reading -->
                        <div>
                            <label for="current_reading" class="block text-sm font-medium text-gray-700 mb-2">
                                Current Reading <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="current_reading" name="current_reading" 
                                   value="{{ old('current_reading', $reading->current_reading) }}" 
                                   step="0.01" min="{{ $reading->previous_reading }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="calculateConsumption()">
                            <p class="mt-1 text-sm text-gray-500">Must be greater than or equal to previous reading ({{ number_format($reading->previous_reading, 2) }})</p>
                        </div>

                        <!-- Billing Period Start -->
                        <div>
                            <label for="billing_period_start" class="block text-sm font-medium text-gray-700 mb-2">
                                Billing Period Start <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="billing_period_start" name="billing_period_start" 
                                   value="{{ old('billing_period_start', $reading->billing_period_start->format('Y-m-d')) }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Billing Period End -->
                        <div>
                            <label for="billing_period_end" class="block text-sm font-medium text-gray-700 mb-2">
                                Billing Period End <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="billing_period_end" name="billing_period_end" 
                                   value="{{ old('billing_period_end', $reading->billing_period_end->format('Y-m-d')) }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Consumption Display -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Consumption Calculation</h4>
                        <div id="consumption-display" class="text-lg font-semibold text-blue-700">
                            @php
                                $consumption = $reading->current_reading - $reading->previous_reading;
                                $unit = $reading->utilityMeter->type === 'electricity' ? 'kWh' : ($reading->utilityMeter->type === 'water' ? 'gallons' : 'cubic feet');
                            @endphp
                            <div class="flex items-center justify-between">
                                <span>Consumption: <strong id="consumption-value">{{ number_format($consumption, 2) }} {{ $unit }}</strong></span>
                                <span class="text-sm" id="calculation-display">({{ number_format($reading->current_reading, 2) }} - {{ number_format($reading->previous_reading, 2) }})</span>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Amount (Display Only) -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-green-900 mb-2">Current Usage Amount</h4>
                        <div class="text-lg font-semibold text-green-700">
                            {{ number_format($consumption, 2) }} {{ $unit }}
                        </div>
                        <p class="text-sm text-green-600 mt-1">This will be used for billing calculations</p>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any additional notes about this reading...">{{ old('notes', $reading->notes) }}</textarea>
                    </div>

                    <!-- Audit Information -->
                    @if($reading->created_at != $reading->updated_at)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Audit Information</h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <div>Created: {{ $reading->created_at->format('M d, Y \a\t g:i A') }}</div>
                                <div>Last Updated: {{ $reading->updated_at->format('M d, Y \a\t g:i A') }}</div>
                            </div>
                        </div>
                    @endif

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('utility-readings.show', $reading) }}" 
                           class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <x-button type="submit">
                            Update Reading
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        function calculateConsumption() {
            const currentReading = parseFloat(document.getElementById('current_reading').value) || 0;
            const previousReading = parseFloat('{{ $reading->previous_reading ?? 0 }}');
            const consumptionValue = document.getElementById('consumption-value');
            const calculationDisplay = document.getElementById('calculation-display');
            const type = '{{ $reading->utilityMeter->type }}';
            const unit = type === 'electricity' ? 'kWh' : (type === 'water' ? 'gallons' : 'cubic feet');
            
            if (currentReading > 0) {
                const consumption = currentReading - previousReading;
                
                if (consumption >= 0) {
                    consumptionValue.textContent = `${consumption.toFixed(2)} ${unit}`;
                    calculationDisplay.textContent = `(${currentReading.toFixed(2)} - ${previousReading.toFixed(2)})`;
                    consumptionValue.className = 'font-semibold text-blue-700';
                } else {
                    consumptionValue.textContent = 'Invalid: Reading cannot be less than previous';
                    calculationDisplay.textContent = '';
                    consumptionValue.className = 'font-semibold text-red-600';
                }
            } else {
                consumptionValue.textContent = '0.00 ' + unit;
                calculationDisplay.textContent = '';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateConsumption();
        });
    </script>
    @endpush
</x-app-layout>
