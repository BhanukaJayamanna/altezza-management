<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mark Bill as Paid') }}
            </h2>
            <x-breadcrumb :items="[
                ['name' => 'Dashboard', 'route' => 'dashboard'],
                ['name' => 'Utility Bills', 'route' => 'utility-bills.index'],
                ['name' => 'Bill #' . $utilityBill->id, 'route' => 'utility-bills.show', 'params' => $utilityBill],
                ['name' => 'Mark as Paid']
            ]" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Record Payment</h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('utility-bills.show', $utilityBill) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Bill
                            </a>
                            <a href="{{ route('utility-bills.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Bills
                            </a>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <strong class="font-bold">Please fix the following errors:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Bill Information -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Utility Bill Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium">Bill ID:</span> #{{ $utilityBill->id }}
                            </div>
                            <div>
                                <span class="font-medium">Apartment:</span> {{ $utilityBill->apartment->number ?? 'N/A' }}
                            </div>
                            <div>
                                <span class="font-medium">Utility Type:</span> {{ ucfirst($utilityBill->utility_type) }}
                            </div>
                            <div>
                                <span class="font-medium">Billing Period:</span> 
                                {{ $utilityBill->billing_period_start ? $utilityBill->billing_period_start->format('M d, Y') : 'N/A' }} - 
                                {{ $utilityBill->billing_period_end ? $utilityBill->billing_period_end->format('M d, Y') : 'N/A' }}
                            </div>
                            <div>
                                <span class="font-medium">Total Amount:</span> 
                                <span class="text-lg font-semibold">LKR {{ number_format($utilityBill->total_amount, 2) }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Amount Paid:</span> 
                                LKR {{ number_format($utilityBill->paid_amount, 2) }}
                            </div>
                            <div>
                                <span class="font-medium">Remaining Balance:</span> 
                                <span class="text-lg font-semibold text-red-600">LKR {{ number_format($utilityBill->remaining_amount, 2) }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Due Date:</span> 
                                <span class="{{ $utilityBill->is_overdue ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $utilityBill->due_date ? $utilityBill->due_date->format('M d, Y') : 'N/A' }}
                                    @if($utilityBill->is_overdue)
                                        (Overdue)
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="font-medium">Status:</span> 
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($utilityBill->status === 'paid') bg-green-100 text-green-800
                                    @elseif($utilityBill->status === 'partial') bg-yellow-100 text-yellow-800
                                    @elseif($utilityBill->is_overdue) bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($utilityBill->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form method="POST" action="{{ route('utility-bills.process-payment', $utilityBill) }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Payment Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Payment Amount</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="amount" id="amount" step="0.01" min="0.01" 
                                           max="{{ $utilityBill->remaining_amount }}" required 
                                           value="{{ old('amount', $utilityBill->remaining_amount) }}"
                                           class="block w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                           placeholder="0.00">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Maximum: LKR {{ number_format($utilityBill->remaining_amount, 2) }}
                                </p>
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                                <select name="payment_method" id="payment_method" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select payment method</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reference Number -->
                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700">Reference Number</label>
                                <input type="text" name="reference_number" id="reference_number" 
                                       value="{{ old('reference_number') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                       placeholder="Transaction ID, Check number, etc.">
                                @error('reference_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Date -->
                            <div>
                                <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date</label>
                                <input type="date" name="payment_date" id="payment_date" 
                                       value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('payment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                          placeholder="Additional notes or comments about this payment...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Payment Summary
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <strong>Bill Total:</strong> LKR {{ number_format($utilityBill->total_amount, 2) }}
                                            </div>
                                            <div>
                                                <strong>Already Paid:</strong> LKR {{ number_format($utilityBill->paid_amount, 2) }}
                                            </div>
                                            <div>
                                                <strong>Remaining:</strong> LKR {{ number_format($utilityBill->remaining_amount, 2) }}
                                            </div>
                                        </div>
                                        <p class="mt-2">
                                            @if($utilityBill->remaining_amount > 0)
                                                <strong>Note:</strong> This payment will {{ $utilityBill->remaining_amount == $utilityBill->total_amount ? 'fully pay' : 'reduce' }} the outstanding balance.
                                            @else
                                                This bill has already been paid in full.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4 mt-6">
                            <a href="{{ route('utility-bills.show', $utilityBill) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-fill full remaining amount by default
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount');
            const remainingAmount = {{ $utilityBill->remaining_amount }};
            
            if (remainingAmount > 0 && !amountInput.value) {
                amountInput.value = remainingAmount.toFixed(2);
            }
        });

        // Validate payment amount doesn't exceed remaining balance
        document.getElementById('amount')?.addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            const maxAmount = {{ $utilityBill->remaining_amount }};
            
            if (amount > maxAmount) {
                this.value = maxAmount.toFixed(2);
            }
        });
    </script>
</x-app-layout>
