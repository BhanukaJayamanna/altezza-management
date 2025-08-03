<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Utility Bill') }}
            </h2>
            <a href="{{ route('utility-bills.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Bills
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
                    <h3 class="text-lg font-medium text-gray-900">Bill Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Create a manual utility bill for an apartment.</p>
                </div>

                <form method="POST" action="{{ route('utility-bills.store') }}" class="p-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Apartment -->
                        <div>
                            <label for="apartment_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Apartment <span class="text-red-500">*</span>
                            </label>
                            <select id="apartment_id" name="apartment_id" required onchange="updateTenantInfo()"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Apartment</option>
                                @foreach($apartments as $apartment)
                                    @php
                                        $tenantName = 'No tenant';
                                        if ($apartment->currentLease && $apartment->currentLease->tenant) {
                                            $tenantName = $apartment->currentLease->tenant->name;
                                        }
                                    @endphp
                                    <option value="{{ $apartment->id }}" 
                                            data-tenant="{{ $tenantName }}"
                                            {{ old('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                        {{ $apartment->number }} - {{ $apartment->type }} ({{ $apartment->block }})
                                    </option>
                                @endforeach
                            </select>
                            <div id="tenant-info" class="mt-1 text-sm text-gray-500"></div>
                        </div>

                        <!-- Meter (Optional) -->
                        <div>
                            <label for="meter_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Associated Meter (Optional)
                            </label>
                            <select id="meter_id" name="meter_id"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">No specific meter</option>
                                @foreach($meters as $meter)
                                    <option value="{{ $meter->id }}" {{ old('meter_id') == $meter->id ? 'selected' : '' }}>
                                        {{ $meter->apartment->number }} - {{ ucfirst($meter->type) }} ({{ $meter->meter_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Billing Period -->
                        <div>
                            <label for="billing_period" class="block text-sm font-medium text-gray-700 mb-2">
                                Billing Period <span class="text-red-500">*</span>
                            </label>
                            <input type="month" id="billing_period" name="billing_period" value="{{ old('billing_period', now()->format('Y-m')) }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Usage Amount -->
                        <div>
                            <label for="usage_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Usage Amount <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="usage_amount" name="usage_amount" value="{{ old('usage_amount') }}" 
                                   step="0.01" min="0" required onchange="calculateTotal()"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0.00">
                            <p class="mt-1 text-sm text-gray-500">Units consumed (kWh, gallons, cubic feet, etc.)</p>
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
                                       step="0.0001" min="0" required onchange="calculateTotal()"
                                       class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0.0000">
                            </div>
                        </div>

                        <!-- Total Amount -->
                        <div>
                            <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Total Amount ($) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" id="total_amount" name="total_amount" value="{{ old('total_amount') }}" 
                                       step="0.01" min="0" required
                                       class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0.00">
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Due Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="due_date" name="due_date" value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any additional notes about this bill...">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('utility-bills.index') }}" 
                           class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <x-button type="submit">
                            Create Bill
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateTenantInfo() {
            const select = document.getElementById('apartment_id');
            const tenantInfo = document.getElementById('tenant-info');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                const tenantName = selectedOption.dataset.tenant;
                tenantInfo.textContent = `Current tenant: ${tenantName}`;
            } else {
                tenantInfo.textContent = '';
            }
        }

        function calculateTotal() {
            const usage = parseFloat(document.getElementById('usage_amount').value) || 0;
            const rate = parseFloat(document.getElementById('rate_per_unit').value) || 0;
            const total = usage * rate;
            
            document.getElementById('total_amount').value = total.toFixed(2);
        }

        // Initialize tenant info on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateTenantInfo();
        });
    </script>
    @endpush
</x-app-layout>
