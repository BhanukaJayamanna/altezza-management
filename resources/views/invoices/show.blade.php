<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Invoice Details') }} - {{ $invoice->invoice_number }}
            </h2>
            <div class="flex space-x-2">
                @if($invoice->status === 'pending' && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
                    <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Invoice
                    </a>
                @endif
                
                @if($invoice->status === 'pending' && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
                    <form action="{{ route('invoices.send-reminder', $invoice) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700" onclick="return confirm('Send payment reminder to {{ $invoice->owner->name }}?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Send Reminder
                        </button>
                    </form>
                    
                    <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700" onclick="return confirm('Are you sure you want to mark this invoice as paid?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark as Paid
                        </button>
                    </form>
                @endif

                @if(auth()->user()->hasRole(['admin', 'manager']))
                    <a href="{{ route('invoices.preview-pdf', $invoice) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Preview PDF
                    </a>

                    <a href="{{ route('invoices.download-pdf', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PDF
                    </a>
                @elseif(auth()->user()->hasRole('owner'))
                    <a href="{{ route('owner.invoices.preview-pdf', $invoice) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Preview PDF
                    </a>

                    <a href="{{ route('owner.invoices.download-pdf', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PDF
                    </a>
                @endif

                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>

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
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Invoice Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <!-- Invoice Header -->
                            <div class="border-b border-gray-200 pb-6 mb-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">{{ $invoice->invoice_number }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($invoice->type === 'rent') bg-blue-100 text-blue-800
                                                @elseif($invoice->type === 'utility') bg-green-100 text-green-800
                                                @elseif($invoice->type === 'maintenance') bg-yellow-100 text-yellow-800
                                                @elseif($invoice->type === 'rooftop_reservation') bg-purple-100 text-purple-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $invoice->type === 'rooftop_reservation' ? 'Rooftop Reservation' : ucfirst($invoice->type) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($invoice->status === 'paid') bg-green-100 text-green-800
                                            @elseif($invoice->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($invoice->status === 'overdue') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                        <p class="text-sm text-gray-600 mt-2">
                                            Issue Date: {{ $invoice->created_at->format('M d, Y') }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Due Date: {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Bill To</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="font-medium text-gray-900">{{ $invoice->owner->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $invoice->owner->email }}</p>
                                        @if($invoice->owner->phone)
                                            <p class="text-sm text-gray-600">{{ $invoice->owner->phone }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Property Details</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="font-medium text-gray-900">Apartment {{ $invoice->apartment->number }}</p>
                                        @if($invoice->apartment->block)
                                            <p class="text-sm text-gray-600">Block {{ $invoice->apartment->block }}</p>
                                        @endif
                                        @if($invoice->apartment->type)
                                            <p class="text-sm text-gray-600">{{ ucfirst($invoice->apartment->type) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Period -->
                            @if($invoice->billing_period_start && $invoice->billing_period_end)
                                <div class="mb-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Billing Period</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($invoice->billing_period_start)->format('M d, Y') }} - 
                                            {{ \Carbon\Carbon::parse($invoice->billing_period_end)->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Line Items -->
                            @if($invoice->line_items)
                                @php
                                    $lineItems = is_string($invoice->line_items) ? json_decode($invoice->line_items, true) : $invoice->line_items;
                                @endphp
                                @if($lineItems && count($lineItems) > 0)
                                    <div class="mb-6">
                                        <h4 class="text-lg font-medium text-gray-900 mb-3">Item Details</h4>
                                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                            <table class="min-w-full divide-y divide-gray-300">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach($lineItems as $item)
                                                        <tr>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                {{ $item['description'] ?? 'N/A' }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                {{ $item['quantity'] ?? 1 }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                LKR {{ number_format($item['rate'] ?? 0, 2) }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                LKR {{ number_format($item['amount'] ?? 0, 2) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            <!-- Description -->
                            @if($invoice->description)
                                <div class="mb-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Description</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm text-gray-600">{{ $invoice->description }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Amount Breakdown -->
                            <div class="border-t border-gray-200 pt-6">
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>Subtotal:</span>
                                        <span>LKR {{ number_format($invoice->amount, 2) }}</span>
                                    </div>
                                    
                                    @if($invoice->late_fee > 0)
                                        <div class="flex justify-between text-sm text-gray-600">
                                            <span>Late Fee:</span>
                                            <span>LKR {{ number_format($invoice->late_fee, 2) }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($invoice->discount > 0)
                                        <div class="flex justify-between text-sm text-green-600">
                                            <span>Discount:</span>
                                            <span>-LKR {{ number_format($invoice->discount, 2) }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex justify-between text-lg font-bold text-gray-900 border-t border-gray-200 pt-3">
                                        <span>Total Amount:</span>
                                        <span>LKR {{ number_format($invoice->total_amount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Payment Information -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                            
                            @if($invoice->status === 'paid')
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Paid On:</span>
                                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($invoice->paid_on)->format('M d, Y') }}</span>
                                    </div>
                                    @if($invoice->payment_method)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Payment Method:</span>
                                            <span class="text-gray-900">{{ ucfirst($invoice->payment_method) }}</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-sm text-gray-600">
                                        @if($invoice->status === 'pending')
                                            Payment pending
                                        @elseif($invoice->status === 'overdue')
                                            Payment overdue
                                        @endif
                                    </p>
                                    @if($invoice->due_date && \Carbon\Carbon::parse($invoice->due_date)->isPast())
                                        <p class="text-xs text-red-600 mt-1">
                                            Due {{ \Carbon\Carbon::parse($invoice->due_date)->diffForHumans() }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Payment History -->
                    @if($invoice->payments && $invoice->payments->count() > 0)
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment History</h3>
                                <div class="space-y-3">
                                    @foreach($invoice->payments as $payment)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">LKR {{ number_format($payment->amount, 2) }}</p>
                                                <p class="text-xs text-gray-600">{{ $payment->created_at->format('M d, Y') }}</p>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @if($payment->status === 'completed') bg-green-100 text-green-800
                                                @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Created By -->
                    @if($invoice->createdBy)
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Created By</h3>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($invoice->createdBy->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $invoice->createdBy->name }}</p>
                                        <p class="text-xs text-gray-600">{{ $invoice->created_at->format('M d, Y g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
