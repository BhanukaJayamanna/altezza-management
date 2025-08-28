<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Payment') }} #{{ $payment->id }}
            </h2>
            <x-breadcrumb :items="[
                ['name' => 'Dashboard', 'route' => 'dashboard'],
                ['name' => 'Payments', 'route' => 'payments.index'],
                ['name' => 'Payment #' . $payment->id, 'route' => 'payments.show', 'params' => $payment],
                ['name' => 'Edit Payment']
            ]" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Edit Payment Details</h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('payments.show', $payment) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Payment
                            </a>
                            <a href="{{ route('payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Payments
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

                    <!-- Invoice Details -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h4 class="text-md font-medium text-gray-900 mb-2">Related Invoice Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium">Invoice:</span> {{ $payment->invoice->invoice_number }}
                            </div>
                            <div>
                                <span class="font-medium">Owner:</span> {{ $payment->invoice->owner->name }}
                            </div>
                            <div>
                                <span class="font-medium">Apartment:</span> {{ $payment->invoice->apartment->number }}
                            </div>
                            <div>
                                <span class="font-medium">Invoice Total:</span> LKR {{ number_format($payment->invoice->total_amount, 2) }}
                            </div>
                            <div>
                                <span class="font-medium">Total Paid:</span> LKR {{ number_format($payment->invoice->payments()->where('status', 'completed')->sum('amount'), 2) }}
                            </div>
                            <div>
                                <span class="font-medium">Remaining:</span> LKR {{ number_format($payment->invoice->total_amount - $payment->invoice->payments()->where('status', 'completed')->sum('amount'), 2) }}
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('payments.update', $payment) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Invoice (Read-only) -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Invoice</label>
                                <div class="mt-1 p-3 bg-gray-100 border border-gray-300 rounded-md">
                                    {{ $payment->invoice->invoice_number }} - {{ $payment->invoice->owner->name }} ({{ $payment->invoice->apartment->number }})
                                    <span class="text-xs text-gray-500 ml-2">(Invoice cannot be changed)</span>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Payment Amount</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="amount" id="amount" step="0.01" min="0.01" required 
                                           value="{{ old('amount', $payment->amount) }}"
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
                                    <option value="cash" {{ old('payment_method', $payment->method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="cheque" {{ old('payment_method', $payment->method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="bank_transfer" {{ old('payment_method', $payment->method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="online" {{ old('payment_method', $payment->method) == 'online' ? 'selected' : '' }}>Online Payment</option>
                                    <option value="card" {{ old('payment_method', $payment->method) == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Date -->
                            <div>
                                <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date</label>
                                <input type="date" name="payment_date" id="payment_date" required 
                                       value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('payment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reference Number -->
                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700">Reference Number</label>
                                <input type="text" name="reference_number" id="reference_number" 
                                       value="{{ old('reference_number', $payment->reference_number) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                       placeholder="Transaction ID, Cheque number, etc.">
                                @error('reference_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Payment Status</label>
                                <select name="status" id="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ old('status', $payment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ old('status', $payment->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="cancelled" {{ old('status', $payment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Current Status Badge -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Current Status</label>
                                <div class="mt-1 p-3 bg-gray-50 border border-gray-300 rounded-md">
                                    @php
                                        $statusLabel = $payment->status_label;
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($statusLabel['color'] === 'green') bg-green-100 text-green-800
                                        @elseif($statusLabel['color'] === 'yellow') bg-yellow-100 text-yellow-800
                                        @elseif($statusLabel['color'] === 'red') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $statusLabel['label'] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                          placeholder="Additional notes or comments...">{{ old('notes', $payment->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Payment History Info -->
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Payment Information
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p><strong>Originally recorded:</strong> {{ $payment->created_at->format('M d, Y H:i') }}</p>
                                        @if($payment->recordedBy)
                                            <p><strong>Recorded by:</strong> {{ $payment->recordedBy->name }} ({{ ucfirst($payment->recordedBy->role) }})</p>
                                        @endif
                                        <p><strong>Last updated:</strong> {{ $payment->updated_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4 mt-6">
                            <a href="{{ route('payments.show', $payment) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Update Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show warning when changing status to failed or cancelled
        document.getElementById('status')?.addEventListener('change', function() {
            const status = this.value;
            const warningDiv = document.getElementById('status-warning');
            
            if (status === 'failed' || status === 'cancelled') {
                if (!warningDiv) {
                    const warning = document.createElement('div');
                    warning.id = 'status-warning';
                    warning.className = 'mt-2 p-2 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded text-sm';
                    warning.innerHTML = '<strong>Warning:</strong> Changing status to ' + status + ' may affect invoice calculations and owner records.';
                    this.parentNode.appendChild(warning);
                }
            } else {
                if (warningDiv) {
                    warningDiv.remove();
                }
            }
        });
    </script>
</x-app-layout>
