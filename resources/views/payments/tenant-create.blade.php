<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Submit Payment') }}
            </h2>
            <x-breadcrumb :items="[
                ['name' => 'Dashboard', 'route' => 'dashboard'],
                ['name' => 'My Payments', 'route' => 'owner.payments'],
                ['name' => 'Submit Payment']
            ]" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Payment Submission</h3>
                        <a href="{{ route('owner.payments') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to My Payments
                        </a>
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

                    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-6">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium">Payment Submission Process:</p>
                                <p class="mt-1">Your payment will be submitted for review and approval by the management. You will receive confirmation once it's processed.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding Invoices -->
                    @if($unpaidInvoices->count() > 0)
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Your Outstanding Invoices</h4>
                            <div class="space-y-3">
                                @foreach($unpaidInvoices as $invoice)
                                    <div class="flex justify-between items-center p-3 bg-white rounded border">
                                        <div>
                                            <div class="font-medium">{{ $invoice->invoice_number }}</div>
                                            <div class="text-sm text-gray-500">{{ $invoice->apartment->number }} - Due: {{ $invoice->due_date->format('M d, Y') }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-medium">@currency($invoice->total_amount)</div>
                                            <div class="text-sm text-gray-500">
                                                @php
                                                    $paid = $invoice->payments()->where('status', 'confirmed')->sum('amount');
                                                    $remaining = $invoice->total_amount - $paid;
                                                @endphp
                                                @if($remaining > 0)
                                                    Remaining: @currency($remaining)
                                                @else
                                                    Fully Paid
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <form method="POST" action="{{ route('owner.payments.store') }}">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Invoice Selection -->
                                <div class="md:col-span-2">
                                    <label for="invoice_id" class="block text-sm font-medium text-gray-700">Select Invoice to Pay</label>
                                    <select name="invoice_id" id="invoice_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Choose an invoice</option>
                                        @foreach($unpaidInvoices as $invoice)
                                            @php
                                                $paid = $invoice->payments()->where('status', 'confirmed')->sum('amount');
                                                $remaining = $invoice->total_amount - $paid;
                                            @endphp
                                            @if($remaining > 0)
                                                <option value="{{ $invoice->id }}" data-total="{{ $invoice->total_amount }}" data-paid="{{ $paid }}" data-remaining="{{ $remaining }}">
                                                    {{ $invoice->invoice_number }} - @currency($remaining) remaining
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('invoice_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Amount -->
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700">Payment Amount</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" required 
                                               value="{{ old('amount') }}"
                                               class="block w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                               placeholder="0.00">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Enter the amount you're paying</p>
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
                                        <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                    </select>
                                    @error('payment_method')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Payment Date -->
                                <div>
                                    <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date</label>
                                    <input type="date" name="payment_date" id="payment_date" required 
                                           value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('payment_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Reference Number -->
                                <div>
                                    <label for="reference_number" class="block text-sm font-medium text-gray-700">Reference Number</label>
                                    <input type="text" name="reference_number" id="reference_number" 
                                           value="{{ old('reference_number') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                           placeholder="Transaction ID, Cheque number, etc.">
                                    <p class="mt-1 text-xs text-gray-500">Optional: Any reference number for this payment</p>
                                    @error('reference_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Notes -->
                                <div class="md:col-span-2">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                    <textarea name="notes" id="notes" rows="3"
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                              placeholder="Any additional information about this payment...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex items-center justify-end space-x-4 mt-6">
                                <a href="{{ route('owner.payments') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Submit Payment
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No outstanding invoices</h3>
                            <p class="mt-1 text-sm text-gray-500">You have no pending invoices to pay at this time.</p>
                            <div class="mt-6">
                                <a href="{{ route('owner.invoices') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    View My Invoices
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-populate amount based on selected invoice
        document.getElementById('invoice_id')?.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const remaining = parseFloat(selected.dataset.remaining || 0);
            
            if (remaining > 0) {
                document.getElementById('amount').value = remaining.toFixed(2);
            }
        });
    </script>
</x-app-layout>
