<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Invoice') }} - {{ $invoice->invoice_number }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View Invoice
                </a>
                <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Invoices
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('invoices.update', $invoice) }}">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Apartment -->
                                <div>
                                    <label for="apartment_id" class="block font-medium text-sm text-gray-700 mb-2">
                                        Apartment <span class="text-red-500">*</span>
                                    </label>
                                    <select id="apartment_id" name="apartment_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('apartment_id') border-red-500 @enderror" required onchange="updateOwner()">
                                        <option value="">Select Apartment</option>
                                        @foreach($apartments as $apartment)
                                            <option value="{{ $apartment->id }}" 
                                                    data-owner-id="{{ $apartment->owner?->id }}"
                                                    data-rent="{{ $apartment->rent_amount }}"
                                                    {{ (old('apartment_id', $invoice->apartment_id) == $apartment->id) ? 'selected' : '' }}>
                                                {{ $apartment->number }} 
                                                @if($apartment->block) - Block {{ $apartment->block }} @endif
                                                ({{ $apartment->owner?->name ?? 'No Owner' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('apartment_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Owner -->
                                <div>
                                    <label for="owner_id" class="block font-medium text-sm text-gray-700 mb-2">
                                        Owner <span class="text-red-500">*</span>
                                    </label>
                                    <select id="owner_id" name="owner_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('owner_id') border-red-500 @enderror" required>
                                        <option value="">Select Owner</option>
                                        @foreach($owners as $owner)
                                            <option value="{{ $owner->id }}" {{ (old('owner_id', $invoice->owner_id) == $owner->id) ? 'selected' : '' }}>
                                                {{ $owner->name }} ({{ $owner->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('owner_id')
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
                                        <option value="rent" {{ (old('type', $invoice->type) == 'rent') ? 'selected' : '' }}>Rent</option>
                                        <option value="utility" {{ (old('type', $invoice->type) == 'utility') ? 'selected' : '' }}>Utility</option>
                                        <option value="maintenance" {{ (old('type', $invoice->type) == 'maintenance') ? 'selected' : '' }}>Maintenance</option>
                                        <option value="other" {{ (old('type', $invoice->type) == 'other') ? 'selected' : '' }}>Other</option>
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
                                           type="date" name="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required />
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
                                          placeholder="Enter invoice description...">{{ old('description', $invoice->description) }}</textarea>
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
                                        Amount (LKR) <span class="text-red-500">*</span>
                                    </label>
                                    <input id="amount" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('amount') border-red-500 @enderror" 
                                           type="number" name="amount" value="{{ old('amount', $invoice->amount) }}" min="0" step="0.01" required onchange="calculateTotal()" />
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
                                           type="number" name="late_fee" value="{{ old('late_fee', $invoice->late_fee ?? 0) }}" min="0" step="0.01" onchange="calculateTotal()" />
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
                                           type="number" name="discount" value="{{ old('discount', $invoice->discount ?? 0) }}" min="0" step="0.01" onchange="calculateTotal()" />
                                    @error('discount')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Billing Period Start -->
                                <div>
                                    <label for="billing_period_start" class="block font-medium text-sm text-gray-700 mb-2">
                                        Billing Period Start
                                    </label>
                                    <input id="billing_period_start" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('billing_period_start') border-red-500 @enderror" 
                                           type="date" name="billing_period_start" value="{{ old('billing_period_start', $invoice->billing_period_start?->format('Y-m-d')) }}" />
                                    @error('billing_period_start')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Billing Period End -->
                                <div>
                                    <label for="billing_period_end" class="block font-medium text-sm text-gray-700 mb-2">
                                        Billing Period End
                                    </label>
                                    <input id="billing_period_end" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('billing_period_end') border-red-500 @enderror" 
                                           type="date" name="billing_period_end" value="{{ old('billing_period_end', $invoice->billing_period_end?->format('Y-m-d')) }}" />
                                    @error('billing_period_end')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Total Amount Display -->
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                                    <span id="total_display" class="text-2xl font-bold text-indigo-600">LKR {{ number_format($invoice->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Current Status Info -->
                        <div class="mb-8 p-4 bg-blue-50 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Current Invoice Status</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Invoice Number:</span>
                                    <p class="font-medium">{{ $invoice->invoice_number }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600">Status:</span>
                                    <p class="font-medium">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($invoice->status === 'paid') bg-green-100 text-green-800
                                            @elseif($invoice->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($invoice->status === 'overdue') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-gray-600">Created:</span>
                                    <p class="font-medium">{{ $invoice->created_at->format('M j, Y') }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600">Total Paid:</span>
                                    <p class="font-medium">LKR {{ number_format($invoice->payments->sum('amount'), 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex items-center space-x-4">
                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update Invoice
                                </button>
                                
                                <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancel
                                </a>
                            </div>
                            
                            <div class="text-sm text-gray-500">
                                <p>Note: Only pending invoices can be edited.</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateOwner() {
            const apartmentSelect = document.getElementById('apartment_id');
            const ownerSelect = document.getElementById('owner_id');
            const selectedOption = apartmentSelect.options[apartmentSelect.selectedIndex];
            
            if (selectedOption.dataset.ownerId) {
                ownerSelect.value = selectedOption.dataset.ownerId;
            } else {
                ownerSelect.value = '';
            }
            
            updateBaseAmount();
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

        // Initialize total calculation
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
    </script>
</x-app-layout>
