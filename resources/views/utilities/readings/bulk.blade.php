<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bulk Utility Readings Entry') }}
            </h2>
            <a href="{{ route('utility-readings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Readings
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Instructions -->
            <x-card>
                <div class="p-6">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Bulk Entry Instructions</h3>
                            <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                <li>Enter readings for multiple meters at once to save time</li>
                                <li>Only active meters are displayed in the form</li>
                                <li>Usage and amount will be calculated automatically based on previous readings</li>
                                <li>Leave reading fields empty if no reading is available for that meter</li>
                                <li>Use the same reading date for all entries, or specify individual dates</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </x-card>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Bulk Entry Form -->
            <form method="POST" action="{{ route('utility-readings.bulk-store') }}" id="bulkReadingsForm">
                @csrf

                <!-- Global Settings -->
                <x-card class="mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Global Settings</h3>
                        <p class="mt-1 text-sm text-gray-600">Apply these settings to all readings below</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Default Reading Date -->
                            <div>
                                <label for="default_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Default Reading Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="default_date" name="default_date" 
                                       value="{{ old('default_date', now()->format('Y-m-d')) }}" required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Utility Type Filter -->
                            <div>
                                <label for="utility_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                    Filter by Utility Type
                                </label>
                                <select id="utility_filter" onchange="filterMeters()" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Utilities</option>
                                    <option value="electricity">Electricity</option>
                                    <option value="water">Water</option>
                                    <option value="gas">Gas</option>
                                </select>
                            </div>

                            <!-- Block Filter -->
                            <div>
                                <label for="block_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                    Filter by Block
                                </label>
                                <select id="block_filter" onchange="filterMeters()" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Blocks</option>
                                    @foreach($blocks as $block)
                                        <option value="{{ $block }}">{{ $block }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </x-card>

                <!-- Meters Grid -->
                <x-card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Meter Readings</h3>
                            <div class="flex space-x-2">
                                <button type="button" onclick="fillAllDates()" 
                                        class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Apply Default Date to All
                                </button>
                                <button type="button" onclick="clearAllReadings()" 
                                        class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Clear All Readings
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="metersGrid">
                            @foreach($meters as $meter)
                            <div class="meter-card bg-gray-50 border border-gray-200 rounded-lg p-4" 
                                 data-utility="{{ $meter->type }}" 
                                 data-assessment-no="{{ $meter->apartment->assessment_no }}">
                                
                                <!-- Meter Header -->
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="font-medium text-gray-900">
                                            {{ $meter->apartment->number }}
                                        </h4>
                                        <p class="text-sm text-gray-500">{{ $meter->apartment->assessment_no }} - {{ ucfirst($meter->type) }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $meter->type === 'electricity' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($meter->type === 'water' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                        {{ $meter->meter_number }}
                                    </span>
                                </div>

                                <!-- Previous Reading Info -->
                                @if($meter->last_reading)
                                <div class="bg-white rounded border p-3 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Last Reading:</span>
                                        <span class="font-mono">{{ number_format($meter->last_reading, 2) }}</span>
                                    </div>
                                    @if($meter->last_reading_date)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Date:</span>
                                        <span>{{ $meter->last_reading_date->format('M j, Y') }}</span>
                                    </div>
                                    @endif
                                </div>
                                @else
                                <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-4">
                                    <p class="text-sm text-blue-800">No previous reading</p>
                                </div>
                                @endif

                                <!-- Reading Input -->
                                <div class="space-y-3">
                                    <div>
                                        <label for="readings[{{ $meter->id }}][reading]" 
                                               class="block text-sm font-medium text-gray-700 mb-1">
                                            New Reading
                                        </label>
                                        <input type="number" 
                                               id="readings[{{ $meter->id }}][reading]" 
                                               name="readings[{{ $meter->id }}][reading]" 
                                               value="{{ old('readings.' . $meter->id . '.reading') }}"
                                               step="0.01" min="0"
                                               data-meter-id="{{ $meter->id }}"
                                               data-last-reading="{{ $meter->last_reading ?? 0 }}"
                                               data-rate="{{ $meter->rate_per_unit }}"
                                               class="reading-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter reading">
                                    </div>

                                    <div>
                                        <label for="readings[{{ $meter->id }}][reading_date]" 
                                               class="block text-sm font-medium text-gray-700 mb-1">
                                            Reading Date
                                        </label>
                                        <input type="date" 
                                               id="readings[{{ $meter->id }}][reading_date]" 
                                               name="readings[{{ $meter->id }}][reading_date]" 
                                               value="{{ old('readings.' . $meter->id . '.reading_date') }}"
                                               class="reading-date block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <!-- Calculated Values -->
                                    <div class="bg-white rounded border p-3">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Usage:</span>
                                            <span id="usage_{{ $meter->id }}" class="font-mono">-</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Amount:</span>
                                            <span id="amount_{{ $meter->id }}" class="font-mono">-</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden inputs -->
                                <input type="hidden" name="readings[{{ $meter->id }}][meter_id]" value="{{ $meter->id }}">
                            </div>
                            @endforeach
                        </div>

                        @if($meters->count() === 0)
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No active meters found</h3>
                            <p class="mt-1 text-sm text-gray-500">Create some utility meters first before entering readings.</p>
                            <div class="mt-6">
                                <a href="{{ route('utility-meters.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Create Meter
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($meters->count() > 0)
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-4">
                        <a href="{{ route('utility-readings.index') }}" 
                           class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <x-button type="submit">
                            Save All Readings
                        </x-button>
                    </div>
                    @endif
                </x-card>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to reading inputs
            const readingInputs = document.querySelectorAll('.reading-input');
            readingInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const meterId = this.dataset.meterId;
                    const lastReading = parseFloat(this.dataset.lastReading);
                    const rate = parseFloat(this.dataset.rate);
                    calculateUsage(meterId, lastReading, rate);
                });
            });
        });

        function filterMeters() {
            const utilityFilter = document.getElementById('utility_filter').value;
            const blockFilter = document.getElementById('block_filter').value;
            const meterCards = document.querySelectorAll('.meter-card');

            meterCards.forEach(card => {
                const cardUtility = card.dataset.utility;
                const cardBlock = card.dataset.block;
                
                const showUtility = !utilityFilter || cardUtility === utilityFilter;
                const showBlock = !blockFilter || cardBlock === blockFilter;
                
                if (showUtility && showBlock) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function fillAllDates() {
            const defaultDate = document.getElementById('default_date').value;
            if (!defaultDate) {
                alert('Please select a default date first.');
                return;
            }
            
            const dateInputs = document.querySelectorAll('.reading-date');
            dateInputs.forEach(input => {
                input.value = defaultDate;
            });
        }

        function clearAllReadings() {
            if (!confirm('Are you sure you want to clear all readings?')) {
                return;
            }
            
            const readingInputs = document.querySelectorAll('input[name*="[reading]"]');
            readingInputs.forEach(input => {
                input.value = '';
            });
            
            // Clear calculated values
            const usageSpans = document.querySelectorAll('[id^="usage_"]');
            const amountSpans = document.querySelectorAll('[id^="amount_"]');
            
            usageSpans.forEach(span => span.textContent = '-');
            amountSpans.forEach(span => span.textContent = '-');
        }

        function calculateUsage(meterId, lastReading, ratePerUnit) {
            const newReadingInput = document.querySelector(`input[name="readings[${meterId}][reading]"]`);
            const newReading = parseFloat(newReadingInput.value);
            
            if (isNaN(newReading) || newReading === '') {
                document.getElementById(`usage_${meterId}`).textContent = '-';
                document.getElementById(`amount_${meterId}`).textContent = '-';
                return;
            }
            
            const usage = Math.max(0, newReading - lastReading);
            const amount = usage * ratePerUnit;
            
            document.getElementById(`usage_${meterId}`).textContent = usage.toFixed(2);
            document.getElementById(`amount_${meterId}`).textContent = '$' + amount.toFixed(2);
        }

        // Auto-fill dates when default date changes
        document.getElementById('default_date').addEventListener('change', function() {
            const emptyDateInputs = document.querySelectorAll('.reading-date');
            emptyDateInputs.forEach(input => {
                if (!input.value) {
                    input.value = this.value;
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
