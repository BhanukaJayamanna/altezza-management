<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Utility Bill Details') }}
            </h2>
            <div class="flex space-x-3">
                @if($bill->status !== 'paid')
                    <a href="{{ route('utility-bills.edit', $bill) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Bill
                    </a>
                @endif
                <a href="{{ route('utility-bills.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Bills
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Bill Summary -->
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Bill Summary</h3>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                            @if($bill->status === 'paid') bg-green-100 text-green-800
                            @elseif($bill->status === 'partial') bg-yellow-100 text-yellow-800
                            @elseif($bill->is_overdue) bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($bill->status) }}
                            @if($bill->is_overdue && $bill->status !== 'paid')
                                (Overdue)
                            @endif
                        </span>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Bill Type</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $bill->type_display }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Billing Period</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ \Carbon\Carbon::createFromFormat('m/Y', $bill->period)->format('F Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Total Amount</label>
                            <p class="mt-1 text-2xl font-bold text-gray-900">@currency($bill->total_amount)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Due Date</label>
                            <p class="mt-1 text-lg font-medium {{ $bill->is_overdue ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $bill->due_date->format('F j, Y') }}
                            </p>
                        </div>
                    </div>

                    @if($bill->paid_amount > 0)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Paid Amount</label>
                                <p class="mt-1 text-lg font-medium text-green-600">@currency($bill->paid_amount)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Remaining Amount</label>
                                <p class="mt-1 text-lg font-medium text-red-600">@currency($bill->remaining_amount)</p>
                            </div>
                            @if($bill->paid_date)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Last Payment Date</label>
                                <p class="mt-1 text-lg font-medium text-gray-900">{{ $bill->paid_date->format('F j, Y') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </x-card>

            <!-- Property & Tenant Information -->
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Property & Tenant Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-base font-medium text-gray-900 mb-4">Apartment Details</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Apartment Number</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <a href="{{ route('apartments.show', $bill->apartment) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            {{ $bill->apartment->number }}
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Block</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $bill->apartment->block ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Type</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $bill->apartment->type ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Floor</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $bill->apartment->floor ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-base font-medium text-gray-900 mb-4">Tenant Information</h4>
                            @if($bill->tenant)
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Tenant Name</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <a href="{{ route('tenants.show', $bill->tenant) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            {{ $bill->tenant->name }}
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $bill->tenant->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $bill->tenant->phone ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @else
                            <p class="text-sm text-gray-500">No tenant assigned to this apartment</p>
                            @endif
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Meter & Usage Information -->
            @if($bill->meter)
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Meter & Usage Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Meter Number</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="{{ route('utility-meters.show', $bill->meter) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    {{ $bill->meter->meter_number }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Meter Type</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($bill->meter->type) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Units Used</label>
                            <p class="mt-1 text-sm text-gray-900">{{ number_format($bill->units_used, 2) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Price per Unit</label>
                            <p class="mt-1 text-sm text-gray-900">@currency($bill->price_per_unit, 4)</p>
                        </div>
                    </div>

                    @if($bill->reading)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-base font-medium text-gray-900 mb-4">Reading Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Current Reading</label>
                                <p class="mt-1 text-sm text-gray-900">{{ number_format($bill->reading->current_reading) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Previous Reading</label>
                                <p class="mt-1 text-sm text-gray-900">{{ number_format($bill->reading->previous_reading) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Reading Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $bill->reading->reading_date->format('F j, Y') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </x-card>
            @endif

            <!-- Payment History -->
            @if($bill->payments->count() > 0)
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Payment History</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recorded By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bill->payments as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->payment_date->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        @currency($payment->amount)
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ucfirst($payment->payment_method) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->recordedBy->name ?? 'System' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->reference_number ?? 'N/A' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-card>
            @endif

            <!-- Notes -->
            @if($bill->notes)
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Notes</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $bill->notes }}</p>
                </div>
            </x-card>
            @endif

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <div class="flex space-x-3">
                    @if($bill->status !== 'paid')
                        <button type="button" onclick="showPaymentModal()" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Record Payment
                        </button>
                    @endif
                    
                    <a href="{{ route('utility-bills.index') }}?apartment_id={{ $bill->apartment_id }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m5 0v-9a2 2 0 00-2-2H8a2 2 0 00-2 2v9m14 0V9a2 2 0 00-2-2h-2M7 21V9M7 9l2-2 2 2"></path>
                        </svg>
                        View All Bills for This Apartment
                    </a>
                </div>

                @if($bill->status !== 'paid')
                <form method="POST" action="{{ route('utility-bills.destroy', $bill) }}" 
                      onsubmit="return confirm('Are you sure you want to delete this utility bill? This action cannot be undone.')"
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Bill
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment Modal (placeholder for future implementation) -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900">Record Payment</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Payment recording functionality will be implemented here.</p>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="hidePaymentModal()" 
                            class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showPaymentModal() {
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function hidePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
