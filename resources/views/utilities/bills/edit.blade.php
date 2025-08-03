<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Utility Bill') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('utility-bills.show', $bill) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    View Bill
                </a>
                <a href="{{ route('utility-bills.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Bills
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

            @if($bill->status === 'paid')
                <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Note:</strong> This bill has been paid and cannot be edited.
                </div>
            @endif

            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Bill Information</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Edit utility bill for {{ $bill->apartment->number }} - {{ $bill->apartment->type }}
                        @if($bill->meter)
                            ({{ ucfirst($bill->meter->type) }} meter: {{ $bill->meter->meter_number }})
                        @endif
                    </p>
                </div>

                <form method="POST" action="{{ route('utility-bills.update', $bill) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Apartment -->
                        <div>
                            <label for="apartment_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Apartment <span class="text-red-500">*</span>
                            </label>
                            <select id="apartment_id" name="apartment_id" required onchange="updateTenantInfo()" {{ $bill->status === 'paid' ? 'disabled' : '' }}
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $bill->status === 'paid' ? 'bg-gray-100' : '' }}">
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
                                            {{ old('apartment_id', $bill->apartment_id) == $apartment->id ? 'selected' : '' }}>
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
                            <select id="meter_id" name="meter_id" {{ $bill->status === 'paid' ? 'disabled' : '' }}
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $bill->status === 'paid' ? 'bg-gray-100' : '' }}">
                                <option value="">No specific meter</option>
                                @foreach($meters as $meter)
                                    <option value="{{ $meter->id }}" {{ old('meter_id', $bill->meter_id) == $meter->id ? 'selected' : '' }}>
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
                            <input type="month" id="billing_period" name="billing_period" 
                                   value="{{ old('billing_period', $bill->period) }}" 
                                   required {{ $bill->status === 'paid' ? 'readonly' : '' }}
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $bill->status === 'paid' ? 'bg-gray-100' : '' }}">
                        </div>

                        <!-- Usage Amount -->
                        <div>
                            <label for="usage_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Usage Amount <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="usage_amount" name="usage_amount" 
                                   value="{{ old('usage_amount', $bill->units_used) }}" 
                                   step="0.01" min="0" required onchange="calculateTotal()" {{ $bill->status === 'paid' ? 'readonly' : '' }}
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $bill->status === 'paid' ? 'bg-gray-100' : '' }}"
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
                                <input type="number" id="rate_per_unit" name="rate_per_unit" 
                                       value="{{ old('rate_per_unit', $bill->price_per_unit) }}" 
                                       step="0.0001" min="0" required onchange="calculateTotal()" {{ $bill->status === 'paid' ? 'readonly' : '' }}
                                       class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $bill->status === 'paid' ? 'bg-gray-100' : '' }}"
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
                                <input type="number" id="total_amount" name="total_amount" 
                                       value="{{ old('total_amount', $bill->total_amount) }}" 
                                       step="0.01" min="0" required {{ $bill->status === 'paid' ? 'readonly' : '' }}
                                       class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $bill->status === 'paid' ? 'bg-gray-100' : '' }}"
                                       placeholder="0.00">
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Due Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="due_date" name="due_date" 
                                   value="{{ old('due_date', $bill->due_date->format('Y-m-d')) }}" 
                                   required {{ $bill->status === 'paid' ? 'readonly' : '' }}
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $bill->status === 'paid' ? 'bg-gray-100' : '' }}">
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <select id="status" name="status" {{ $bill->status === 'paid' ? 'disabled' : '' }}
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $bill->status === 'paid' ? 'bg-gray-100' : '' }}">
                                <option value="unpaid" {{ old('status', $bill->status) === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="sent" {{ old('status', $bill->status) === 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="overdue" {{ old('status', $bill->status) === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                <option value="paid" {{ old('status', $bill->status) === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea id="notes" name="notes" rows="3" {{ $bill->status === 'paid' ? 'readonly' : '' }}
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $bill->status === 'paid' ? 'bg-gray-100' : '' }}"
                                  placeholder="Any additional notes about this bill...">{{ old('notes', $bill->notes) }}</textarea>
                    </div>

                    <!-- Payment Information (if paid) -->
                    @if($bill->status === 'paid' && $bill->payments->count() > 0)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="text-lg font-medium text-green-800 mb-3">Payment Information</h4>
                            @foreach($bill->payments as $payment)
                                <div class="flex justify-between items-center py-2 {{ !$loop->last ? 'border-b border-green-200' : '' }}">
                                    <div>
                                        <span class="text-sm font-medium text-green-700">
                                            @currency($payment->amount)
                                        </span>
                                        <span class="text-sm text-green-600 ml-2">
                                            {{ $payment->payment_method }} - {{ $payment->payment_date->format('M j, Y') }}
                                        </span>
                                    </div>
                                    @if($payment->notes)
                                        <span class="text-sm text-green-600">{{ $payment->notes }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('utility-bills.show', $bill) }}" 
                           class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        @if($bill->status !== 'paid')
                            <x-button type="submit">
                                Update Bill
                            </x-button>
                        @endif
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
            // Only calculate if bill is not paid
            const statusField = document.getElementById('status');
            if (statusField && statusField.value === 'paid') {
                return;
            }
            
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
