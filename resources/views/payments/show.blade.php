<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Payment Details') }} - #{{ $payment->id }}
            </h2>
            <div class="flex space-x-2">
                @if($payment->status === 'pending' && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
                    <form action="{{ route('payments.approve', $payment) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700" onclick="return confirm('Are you sure you want to approve this payment?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Approve Payment
                        </button>
                    </form>
                @endif

                @if($payment->status !== 'cancelled' && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
                    <a href="{{ route('payments.edit', $payment) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Payment
                    </a>
                @endif

                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>

                <a href="{{ route('payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Payments
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Payment Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <!-- Payment Header -->
                            <div class="border-b border-gray-200 pb-6 mb-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">Payment #{{ $payment->id }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Amount: <span class="font-semibold text-lg">${{ number_format($payment->amount, 2) }}</span>
                                        </p>
                                    </div>
                                    <div class="text-right">
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
                                        <p class="text-sm text-gray-600 mt-2">
                                            Payment Date: {{ $payment->payment_date->format('M d, Y') }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Recorded: {{ $payment->created_at->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Payment Details</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Method</label>
                                            <p class="text-sm text-gray-900">{{ $payment->method_label }}</p>
                                        </div>
                                        @if($payment->reference_number)
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Reference Number</label>
                                                <p class="text-sm text-gray-900">{{ $payment->reference_number }}</p>
                                            </div>
                                        @endif
                                        @if($payment->notes)
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Notes</label>
                                                <p class="text-sm text-gray-900">{{ $payment->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Tenant Information</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="font-medium text-gray-900">{{ $payment->tenant->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $payment->tenant->email }}</p>
                                        @if($payment->tenant->phone)
                                            <p class="text-sm text-gray-600">{{ $payment->tenant->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Information -->
                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-3">Related Invoice</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $payment->invoice->invoice_number }}</p>
                                            <p class="text-sm text-gray-600">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($payment->invoice->type === 'rent') bg-blue-100 text-blue-800
                                                    @elseif($payment->invoice->type === 'utility') bg-green-100 text-green-800
                                                    @elseif($payment->invoice->type === 'maintenance') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst($payment->invoice->type) }}
                                                </span>
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                Apartment {{ $payment->invoice->apartment->number }}
                                                @if($payment->invoice->apartment->block)
                                                    - Block {{ $payment->invoice->apartment->block }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">
                                                Total: ${{ number_format($payment->invoice->total_amount, 2) }}
                                            </p>
                                            <a href="{{ route('invoices.show', $payment->invoice) }}" class="text-sm text-indigo-600 hover:text-indigo-900 mt-1 inline-block">
                                                View Invoice →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($payment->receipt_file)
                                <!-- Receipt File -->
                                <div class="mb-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Receipt</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <a href="{{ asset('storage/' . $payment->receipt_file) }}" target="_blank" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download Receipt
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Summary</h3>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Payment Amount</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payment->amount, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Payment Method</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $payment->method_label }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $payment->status_label['label'] }}</span>
                                </div>
                                
                                <div class="flex justify-between pt-4 border-t border-gray-200">
                                    <span class="text-sm text-gray-600">Invoice Total</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payment->invoice->total_amount, 2) }}</span>
                                </div>
                                
                                @php
                                    $totalPaid = $payment->invoice->payments()->where('status', 'completed')->sum('amount');
                                    $remainingBalance = $payment->invoice->total_amount - $totalPaid;
                                @endphp
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Total Paid</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($totalPaid, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Remaining Balance</span>
                                    <span class="text-sm font-medium {{ $remainingBalance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        ${{ number_format($remainingBalance, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recorded By -->
                    @if($payment->recordedBy)
                        <div class="bg-white overflow-hidden shadow rounded-lg mt-6">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Recorded By</h3>
                                
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ substr($payment->recordedBy->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $payment->recordedBy->name }}</p>
                                        <p class="text-sm text-gray-500">{{ ucfirst($payment->recordedBy->role) }}</p>
                                        <p class="text-sm text-gray-500">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            .print-only {
                display: block !important;
            }
            
            body {
                font-size: 12px;
            }
            
            .bg-gray-50 {
                background-color: #f9f9f9 !important;
            }
        }
    </style>
    @endpush
</x-app-layout>
