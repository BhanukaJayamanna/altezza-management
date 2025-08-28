<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Management Fee Invoice - {{ $invoice->invoice_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('management-fees.quarterly-invoices') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Invoices
                </a>
                <a href="{{ route('management-fees.download-invoice', $invoice) }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                   target="_blank">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </a>
                <a href="{{ route('management-fees.print-invoice', $invoice) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                   target="_blank">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </a>
                @if($invoice->status === 'pending')
                <button onclick="openMarkPaidModal()" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Mark as Paid
                </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Invoice Status -->
            <div class="mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="mr-4">
                                @if($invoice->status === 'paid')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Paid
                                    </span>
                                @elseif($invoice->due_date < now())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Overdue
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Pending
                                    </span>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Invoice {{ $invoice->invoice_number }}</h3>
                                <p class="text-gray-600">{{ $invoice->apartment->number }} - {{ $invoice->owner->name ?? 'No Owner' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900">Rs. {{ number_format($invoice->net_total, 2) }}</div>
                            <div class="text-gray-600">Due: {{ $invoice->due_date->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Preview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-0">
                    @php
                        // Calculate additional data for the invoice template
                        $managementCorp = $invoice->apartment->managementCorporation ?? new stdClass();
                        $managementCorp->plan_number = '7538';
                        $managementCorp->registration_number = 'CMA/CCU/2023/PVT/MC/1018';
                        $managementCorp->address = 'No. 202/1, AVERIWATTA ROAD, HUNUPITIYA, WATTALA';
                        $managementCorp->email = 'propertymanager.altezza@gmail.com';
                        
                        // Mock data for demonstration - you can replace with real calculations
                        $previousOutstanding = 258852.58;
                        $totalPayments = 144523.94;
                        $currentOutstanding = $previousOutstanding - $totalPayments;
                        $previousPeriodEnd = '1st JANUARY ' . $invoice->year;
                        $paymentsAsOf = '25th MARCH ' . $invoice->year;
                        
                        $bankDetails = [
                            'account_name' => 'THE MCCP NO. 7538 ALTEZZA APARTMENT',
                            'account_number' => '035010047455',
                            'account_type' => 'CURRENT ACCOUNT',
                            'bank_name' => 'Hatton National Bank (7083)',
                            'branch_name' => 'Wattala (035)',
                            'swift_code' => 'HBLILKLX'
                        ];
                    @endphp
                    
                    @include('management-fees.invoice-template', [
                        'invoice' => $invoice,
                        'managementCorp' => $managementCorp,
                        'previousOutstanding' => $previousOutstanding,
                        'totalPayments' => $totalPayments,
                        'currentOutstanding' => $currentOutstanding,
                        'previousPeriodEnd' => $previousPeriodEnd,
                        'paymentsAsOf' => $paymentsAsOf,
                        'bankDetails' => $bankDetails
                    ])
                </div>
            </div>
        </div>
    </div>

    <!-- Mark as Paid Modal -->
    @if($invoice->status === 'pending')
    <div id="markPaidModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Mark Invoice as Paid</h3>
                    <button onclick="closeMarkPaidModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('management-fees.mark-invoice-paid', $invoice) }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-4">
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select payment method</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                            <option value="online">Online Payment</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="payment_reference" class="block text-sm font-medium text-gray-700">Payment Reference</label>
                        <input type="text" name="payment_reference" id="payment_reference" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Transaction ID, Cheque number, etc.">
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeMarkPaidModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Mark as Paid
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script>
        function openMarkPaidModal() {
            document.getElementById('markPaidModal').classList.remove('hidden');
        }
        
        function closeMarkPaidModal() {
            document.getElementById('markPaidModal').classList.add('hidden');
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('markPaidModal');
            if (event.target === modal) {
                closeMarkPaidModal();
            }
        });
    </script>
</x-app-layout>
