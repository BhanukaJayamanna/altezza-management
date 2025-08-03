<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Payment Voucher - {{ $voucher->voucher_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('vouchers.export-pdf', $voucher) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('vouchers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Status and Actions -->
            <x-card>
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                @if($voucher->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($voucher->status == 'approved') bg-green-100 text-green-800
                                @elseif($voucher->status == 'rejected') bg-red-100 text-red-800
                                @elseif($voucher->status == 'paid') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($voucher->status) }}
                            </span>
                            
                            @if($voucher->approved_at)
                                <div class="text-sm text-gray-600">
                                    {{ $voucher->status == 'approved' ? 'Approved' : 'Rejected' }} by {{ $voucher->approver->name }} 
                                    on {{ $voucher->approved_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>

                        <div class="flex space-x-2">
                            @can('edit_vouchers')
                                @if($voucher->isPending())
                                    <a href="{{ route('vouchers.edit', $voucher) }}" class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                        Edit
                                    </a>
                                @endif
                            @endcan

                            @can('approve_vouchers')
                                @if($voucher->isPending())
                                    <button onclick="showApprovalModal()" class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                        Approve
                                    </button>
                                    <button onclick="showRejectionModal()" class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                        Reject
                                    </button>
                                @elseif($voucher->isApproved())
                                    <button onclick="showPaymentModal()" class="inline-flex items-center px-3 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                                        Mark as Paid
                                    </button>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </x-card>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Voucher Details -->
                <x-card>
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Voucher Details</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Voucher Number:</dt>
                                <dd class="text-sm text-gray-900 font-medium">{{ $voucher->voucher_number }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Date:</dt>
                                <dd class="text-sm text-gray-900">{{ $voucher->voucher_date->format('d/m/Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Category:</dt>
                                <dd class="text-sm text-gray-900">{{ ucfirst($voucher->expense_category) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Amount:</dt>
                                <dd class="text-lg font-bold text-gray-900">₹{{ number_format($voucher->amount, 2) }}</dd>
                            </div>
                            @if($voucher->apartment)
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Related Apartment:</dt>
                                    <dd class="text-sm text-gray-900">{{ $voucher->apartment->number }}</dd>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Created By:</dt>
                                <dd class="text-sm text-gray-900">{{ $voucher->creator->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Created On:</dt>
                                <dd class="text-sm text-gray-900">{{ $voucher->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </x-card>

                <!-- Vendor Information -->
                <x-card>
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Vendor Information</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Name:</dt>
                                <dd class="text-sm text-gray-900 font-medium">{{ $voucher->vendor_name }}</dd>
                            </div>
                            @if($voucher->vendor_phone)
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Phone:</dt>
                                    <dd class="text-sm text-gray-900">{{ $voucher->vendor_phone }}</dd>
                                </div>
                            @endif
                            @if($voucher->vendor_email)
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Email:</dt>
                                    <dd class="text-sm text-gray-900">{{ $voucher->vendor_email }}</dd>
                                </div>
                            @endif
                            @if($voucher->vendor_address)
                                <div class="pt-2">
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Address:</dt>
                                    <dd class="text-sm text-gray-900">{{ $voucher->vendor_address }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </x-card>

                <!-- Payment Information -->
                <x-card>
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Payment Method:</dt>
                                <dd class="text-sm text-gray-900">{{ $voucher->payment_method_display }}</dd>
                            </div>
                            @if($voucher->reference_number)
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Reference:</dt>
                                    <dd class="text-sm text-gray-900">{{ $voucher->reference_number }}</dd>
                                </div>
                            @endif
                            @if($voucher->payment_date)
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Payment Date:</dt>
                                    <dd class="text-sm text-gray-900">{{ $voucher->payment_date->format('d/m/Y') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </x-card>

                <!-- Receipt/Attachment -->
                @if($voucher->receipt_file)
                    <x-card>
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Attached Receipt</h3>
                            <div class="flex items-center space-x-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Receipt File</p>
                                    <a href="{{ Storage::url($voucher->receipt_file) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-500">
                                        View Attachment
                                    </a>
                                </div>
                            </div>
                        </div>
                    </x-card>
                @endif
            </div>

            <!-- Description -->
            <x-card>
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Description / Purpose</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-900 whitespace-pre-line">{{ $voucher->description }}</p>
                    </div>
                </div>
            </x-card>

            <!-- Approval Notes -->
            @if($voucher->approval_notes)
                <x-card>
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            {{ $voucher->status == 'approved' ? 'Approval' : 'Rejection' }} Notes
                        </h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-900 whitespace-pre-line">{{ $voucher->approval_notes }}</p>
                        </div>
                    </div>
                </x-card>
            @endif
        </div>
    </div>

    <!-- Approval Modal -->
    <div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Approve Voucher</h3>
                <form method="POST" action="{{ route('vouchers.approve', $voucher) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="approval_notes" class="block text-sm font-medium text-gray-700">Approval Notes (Optional)</label>
                        <textarea name="approval_notes" id="approval_notes" rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                  placeholder="Add any notes..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideApprovalModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Voucher</h3>
                <form method="POST" action="{{ route('vouchers.reject', $voucher) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="rejection_notes" class="block text-sm font-medium text-gray-700">Rejection Reason *</label>
                        <textarea name="approval_notes" id="rejection_notes" rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                  placeholder="Please provide reason for rejection..." required></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideRejectionModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Mark as Paid</h3>
                <form method="POST" action="{{ route('vouchers.mark-paid', $voucher) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date *</label>
                        <input type="date" name="payment_date" id="payment_date" value="{{ date('Y-m-d') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hidePaymentModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Mark as Paid</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showApprovalModal() {
            document.getElementById('approvalModal').classList.remove('hidden');
        }
        
        function hideApprovalModal() {
            document.getElementById('approvalModal').classList.add('hidden');
        }

        function showRejectionModal() {
            document.getElementById('rejectionModal').classList.remove('hidden');
        }
        
        function hideRejectionModal() {
            document.getElementById('rejectionModal').classList.add('hidden');
        }

        function showPaymentModal() {
            document.getElementById('paymentModal').classList.remove('hidden');
        }
        
        function hidePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
