<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Record Payment') }}
            </h2>
            <x-breadcrumb :items="[
                ['name' => 'Dashboard', 'route' => 'dashboard'],
                ['name' => 'Payments', 'route' => 'payments.index'],
                ['name' => 'Record Payment']
            ]" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Payment Details</h3>
                        <a href="{{ route('payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Payments
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

                    @if(isset($invoice))
                        <!-- Invoice Details -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h4 class="text-md font-medium text-gray-900 mb-2">Invoice Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="font-medium">Invoice:</span> {{ $invoice->invoice_number }}
                                </div>
                                <div>
                                    <span class="font-medium">Owner:</span> {{ $invoice->owner->name }}
                                </div>
                                <div>
                                    <span class="font-medium">Apartment:</span> {{ $invoice->apartment->number }}
                                </div>
                                <div>
                                    <span class="font-medium">Total Amount:</span> @currency($invoice->total_amount)
                                </div>
                                <div>
                                    <span class="font-medium">Paid Amount:</span> @currency($invoice->payments()->where('status', 'confirmed')->sum('amount'))
                                </div>
                                <div>
                                    <span class="font-medium">Remaining:</span> @currency($invoice->total_amount - $invoice->payments()->where('status', 'confirmed')->sum('amount'))
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('payments.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Invoice Selection -->
                            <div class="md:col-span-2">
                                <label for="invoice_id" class="block text-sm font-medium text-gray-700">Invoice</label>
                                @if(isset($invoice))
                                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                    <div class="mt-1 p-3 bg-gray-100 border border-gray-300 rounded-md">
                                        {{ $invoice->invoice_number }} - {{ $invoice->owner->name }} ({{ $invoice->apartment->number }})
                                    </div>
                                @else
                                    <select name="invoice_id" id="invoice_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select an invoice</option>
                                        @foreach($unpaidInvoices as $unpaidInvoice)
                                            <option value="{{ $unpaidInvoice->id }}" data-total="{{ $unpaidInvoice->total_amount }}" data-paid="{{ $unpaidInvoice->payments()->where('status', 'confirmed')->sum('amount') }}">
                                                {{ $unpaidInvoice->invoice_number }} - {{ $unpaidInvoice->owner->name }} ({{ $unpaidInvoice->apartment->number }}) - Remaining: @currency($unpaidInvoice->total_amount - $unpaidInvoice->payments()->where('status', 'confirmed')->sum('amount'))
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
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
                                           value="{{ old('amount', isset($invoice) ? $invoice->total_amount - $invoice->payments()->where('status', 'confirmed')->sum('amount') : '') }}"
                                           class="block w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                           placeholder="0.00">
                                </div>
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
                                @error('reference_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                          placeholder="Additional notes or comments...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4 mt-6">
                            <a href="{{ route('payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-populate amount based on selected invoice
        document.getElementById('invoice_id')?.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const total = parseFloat(selected.dataset.total || 0);
            const paid = parseFloat(selected.dataset.paid || 0);
            const remaining = total - paid;
            
            if (remaining > 0) {
                document.getElementById('amount').value = remaining.toFixed(2);
            }
        });
    </script>
</x-app-layout>
