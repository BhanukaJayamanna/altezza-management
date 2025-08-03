<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Invoice') }}
            </h2>
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Invoices
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('invoices.store') }}">
                        @csrf

                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Apartment -->
                                <div>
                                    <label for="apartment_id" class="block font-medium text-sm text-gray-700 mb-2">
                                        Apartment <span class="text-red-500">*</span>
                                    </label>
                                    <select id="apartment_id" name="apartment_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('apartment_id') border-red-500 @enderror" required onchange="updateTenant()">
                                        <option value="">Select Apartment</option>
                                        @foreach($apartments as $apartment)
                                            <option value="{{ $apartment->id }}" 
                                                    data-tenant-id="{{ $apartment->tenant?->id }}"
                                                    data-rent="{{ $apartment->rent_amount }}"
                                                    {{ old('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                                {{ $apartment->number }} 
                                                @if($apartment->block) - Block {{ $apartment->block }} @endif
                                                ({{ $apartment->tenant?->name ?? 'No Tenant' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('apartment_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Tenant -->
                                <div>
                                    <label for="tenant_id" class="block font-medium text-sm text-gray-700 mb-2">
                                        Tenant <span class="text-red-500">*</span>
                                    </label>
                                    <select id="tenant_id" name="tenant_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('tenant_id') border-red-500 @enderror" required>
                                        <option value="">Select Tenant</option>
                                        @foreach($tenants as $tenant)
                                            <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                                {{ $tenant->name }} ({{ $tenant->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tenant_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Lease (Optional) -->
                                <div>
                                    <label for="lease_id" class="block font-medium text-sm text-gray-700 mb-2">
                                        Related Lease (Optional)
                                    </label>
                                    <select id="lease_id" name="lease_id" onchange="updateFromLease()" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('lease_id') border-red-500 @enderror">
                                        <option value="">Select Lease (Optional)</option>
                                        @foreach($leases as $lease)
                                            <option value="{{ $lease->id }}" 
                                                    data-apartment-id="{{ $lease->apartment_id }}"
                                                    data-tenant-id="{{ $lease->tenant_id }}"
                                                    data-rent="{{ $lease->rent_amount }}"
                                                    {{ old('lease_id') == $lease->id ? 'selected' : '' }}>
                                                {{ $lease->lease_number }} - Apt {{ $lease->apartment->number }} ({{ $lease->tenant->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lease_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Type -->
                                <div>
                                    <label for="type" class="block font-medium text-sm text-gray-700 mb-2">
                                        Invoice Type <span class="text-red-500">*</span>
                                    </label>
                                    <select id="type" name="type" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('type') border-red-500 @enderror" required onchange="updateBaseAmount()">
                                        <option value="">Select Type</option>
                                        <option value="rent" {{ old('type') == 'rent' ? 'selected' : '' }}>Rent</option>
                                        <option value="utility" {{ old('type') == 'utility' ? 'selected' : '' }}>Utility</option>
                                        <option value="maintenance" {{ old('type') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('type')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Due Date -->
                                <div>
                                    <label for="due_date" class="block font-medium text-sm text-gray-700 mb-2">
                                        Due Date <span class="text-red-500">*</span>
                                    </label>
                                    <input id="due_date" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('due_date') border-red-500 @enderror" 
                                           type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required />
                                    @error('due_date')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mt-6">
                                <label for="description" class="block font-medium text-sm text-gray-700 mb-2">
                                    Description
                                </label>
                                <textarea id="description" name="description" rows="3" 
                                          class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror"
                                          placeholder="Enter invoice description...">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Amount Details -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Amount Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Base Amount -->
                                <div>
                                    <label for="amount" class="block font-medium text-sm text-gray-700 mb-2">
                                        Amount ($) <span class="text-red-500">*</span>
                                    </label>
                                    <input id="amount" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('amount') border-red-500 @enderror" 
                                           type="number" name="amount" value="{{ old('amount') }}" min="0" step="0.01" required onchange="calculateTotal()" />
                                    @error('amount')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Late Fee -->
                                <div>
                                    <label for="late_fee" class="block font-medium text-sm text-gray-700 mb-2">
                                        Late Fee ($)
                                    </label>
                                    <input id="late_fee" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('late_fee') border-red-500 @enderror" 
                                           type="number" name="late_fee" value="{{ old('late_fee', 0) }}" min="0" step="0.01" onchange="calculateTotal()" />
                                    @error('late_fee')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Discount -->
                                <div>
                                    <label for="discount" class="block font-medium text-sm text-gray-700 mb-2">
                                        Discount ($)
                                    </label>
                                    <input id="discount" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('discount') border-red-500 @enderror" 
                                           type="number" name="discount" value="{{ old('discount', 0) }}" min="0" step="0.01" onchange="calculateTotal()" />
                                    @error('discount')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Billing Period -->
                                <div>
                                    <label for="billing_period_start" class="block font-medium text-sm text-gray-700 mb-2">
                                        Billing Period Start
                                    </label>
                                    <input id="billing_period_start" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('billing_period_start') border-red-500 @enderror" 
                                           type="date" name="billing_period_start" value="{{ old('billing_period_start') }}" />
                                    @error('billing_period_start')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Total Amount Display -->
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                                    <span id="total_display" class="text-2xl font-bold text-indigo-600">$0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Line Items (Optional) -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Line Items (Optional)</h3>
                                <button type="button" onclick="addLineItem()" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    + Add Item
                                </button>
                            </div>
                            <div id="line-items">
                                <!-- Line items will be added here dynamically -->
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex items-center space-x-4">
                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Create Invoice
                                </button>
                                
                                <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let lineItemCount = 0;

        function updateTenant() {
            const apartmentSelect = document.getElementById('apartment_id');
            const tenantSelect = document.getElementById('tenant_id');
            const selectedOption = apartmentSelect.options[apartmentSelect.selectedIndex];
            
            if (selectedOption.dataset.tenantId) {
                tenantSelect.value = selectedOption.dataset.tenantId;
            } else {
                tenantSelect.value = '';
            }
            
            updateBaseAmount();
        }

        function updateFromLease() {
            const leaseSelect = document.getElementById('lease_id');
            const apartmentSelect = document.getElementById('apartment_id');
            const tenantSelect = document.getElementById('tenant_id');
            const amountInput = document.getElementById('amount');
            const selectedOption = leaseSelect.options[leaseSelect.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                // Update apartment
                if (selectedOption.dataset.apartmentId) {
                    apartmentSelect.value = selectedOption.dataset.apartmentId;
                }
                
                // Update tenant
                if (selectedOption.dataset.tenantId) {
                    tenantSelect.value = selectedOption.dataset.tenantId;
                }
                
                // Update amount if it's a rent invoice
                const typeSelect = document.getElementById('type');
                if (typeSelect.value === 'rent' && selectedOption.dataset.rent) {
                    amountInput.value = selectedOption.dataset.rent;
                }
            }
        }

        function updateBaseAmount() {
            const apartmentSelect = document.getElementById('apartment_id');
            const typeSelect = document.getElementById('type');
            const amountInput = document.getElementById('amount');
            const selectedOption = apartmentSelect.options[apartmentSelect.selectedIndex];
            
            if (typeSelect.value === 'rent' && selectedOption.dataset.rent) {
                amountInput.value = selectedOption.dataset.rent;
            }
            
            calculateTotal();
        }

        function calculateTotal() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            const lateFee = parseFloat(document.getElementById('late_fee').value) || 0;
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            
            const total = amount + lateFee - discount;
            document.getElementById('total_display').textContent = '$' + total.toFixed(2);
        }

        function addLineItem() {
            lineItemCount++;
            const lineItemsContainer = document.getElementById('line-items');
            const lineItemHtml = `
                <div class="flex gap-4 mb-4 items-end" id="line-item-${lineItemCount}">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" name="items[${lineItemCount}][description]" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Item description">
                    </div>
                    <div class="w-32">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                        <input type="number" name="items[${lineItemCount}][amount]" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               min="0" step="0.01" placeholder="0.00">
                    </div>
                    <button type="button" onclick="removeLineItem(${lineItemCount})" 
                            class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 mb-1">
                        Remove
                    </button>
                </div>
            `;
            lineItemsContainer.insertAdjacentHTML('beforeend', lineItemHtml);
        }

        function removeLineItem(id) {
            document.getElementById(`line-item-${id}`).remove();
        }

        // Initialize total calculation
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
    </script>
</x-app-layout>
