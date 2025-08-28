<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                    {{ __('Quarterly Management Fee Invoices') }}
                </h2>
                <p class="text-slate-600 text-sm mt-1">View and manage quarterly invoices for Q{{ $quarter }} {{ $year }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('management-fees.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <button onclick="openGenerateModal()" 
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-plus mr-2"></i>Generate Invoices
                </button>
                <button onclick="openManualEntryModal()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-edit mr-2"></i>Manual Entry
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Quarter Selection -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Filter by Quarter</h3>
                    <form method="GET" class="flex items-center space-x-4">
                        <select name="quarter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="1" {{ $quarter == 1 ? 'selected' : '' }}>Q1 (Jan-Mar)</option>
                            <option value="2" {{ $quarter == 2 ? 'selected' : '' }}>Q2 (Apr-Jun)</option>
                            <option value="3" {{ $quarter == 3 ? 'selected' : '' }}>Q3 (Jul-Sep)</option>
                            <option value="4" {{ $quarter == 4 ? 'selected' : '' }}>Q4 (Oct-Dec)</option>
                        </select>
                        <select name="year" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @for ($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                            Filter
                        </button>
                    </form>
                </div>
            </div>

            <!-- Statistics Cards -->
            @if(isset($stats))
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-500 bg-opacity-75">
                            <i class="fas fa-file-invoice text-white"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Invoices</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_invoices'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500 bg-opacity-75">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Paid</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['paid_invoices'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-500 bg-opacity-75">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pending</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_invoices'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-500 bg-opacity-75">
                            <i class="fas fa-dollar-sign text-white"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Amount</p>
                            <p class="text-2xl font-semibold text-gray-900">Rs. {{ number_format($stats['total_amount'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Invoices Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Invoices for Q{{ $quarter }} {{ $year }}</h3>
                </div>
                
                @if($invoices->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apartment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area (sq ft)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($invoices as $invoice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $invoice->invoice_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $invoice->apartment->number ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-medium">{{ number_format($invoice->area_sqft ?? $invoice->apartment->area ?? 0, 0) }}</span>
                                    <span class="text-gray-500 text-xs ml-1">sq ft</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $invoice->owner->name ?? 'No Owner' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rs. {{ number_format($invoice->net_total, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $invoice->due_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($invoice->status === 'paid')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Paid
                                        </span>
                                    @elseif($invoice->due_date < now())
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Overdue
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('management-fees.show-invoice', $invoice) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">View</a>
                                        <a href="{{ route('management-fees.download-invoice', $invoice) }}" 
                                           class="text-green-600 hover:text-green-900">PDF</a>
                                        @if($invoice->status === 'pending')
                                        <button onclick="markAsPaid({{ $invoice->id }})" 
                                                class="text-blue-600 hover:text-blue-900">Mark Paid</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $invoices->links() }}
                </div>
                @else
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-file-invoice text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No invoices found</h3>
                    <p class="text-gray-500 mb-4">No invoices have been generated for Q{{ $quarter }} {{ $year }} yet.</p>
                    <button onclick="openGenerateModal()" 
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                        Generate Invoices Now
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Generate Invoices Modal -->
    <div id="generateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Generate Quarterly Invoices</h3>
                    <button onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('management-fees.generate-quarterly') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="gen_quarter" class="block text-sm font-medium text-gray-700">Quarter</label>
                        <select name="quarter" id="gen_quarter" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="1" {{ $quarter == 1 ? 'selected' : '' }}>Q1 (Jan-Mar)</option>
                            <option value="2" {{ $quarter == 2 ? 'selected' : '' }}>Q2 (Apr-Jun)</option>
                            <option value="3" {{ $quarter == 3 ? 'selected' : '' }}>Q3 (Jul-Sep)</option>
                            <option value="4" {{ $quarter == 4 ? 'selected' : '' }}>Q4 (Oct-Dec)</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="gen_year" class="block text-sm font-medium text-gray-700">Year</label>
                        <select name="year" id="gen_year" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @for ($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeGenerateModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Generate Invoices
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Manual Entry Modal -->
    <div id="manualEntryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-10 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Manual Fee Entry</h3>
                    <button onclick="closeManualEntryModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('management-fees.manual-entry') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="apartment_id" class="block text-sm font-medium text-gray-700">Apartment</label>
                        <select name="apartment_id" id="apartment_id" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                onchange="updateApartmentArea()">
                            <option value="">Select Apartment</option>
                            @foreach(\App\Models\Apartment::with('currentOwner')->get() as $apartment)
                                <option value="{{ $apartment->id }}" data-area="{{ $apartment->area ?? 0 }}">
                                    {{ $apartment->number }} - {{ $apartment->currentOwner->name ?? 'No Owner' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Area Display -->
                    <div class="mb-4" id="areaDisplay" style="display: none;">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Apartment Area:</span>
                                <span id="apartmentArea" class="text-lg font-semibold text-indigo-600">0 sq ft</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Management fees will be calculated based on this area
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="management_fee" class="block text-sm font-medium text-gray-700">Management Fee</label>
                            <input type="number" name="management_fee" id="management_fee" step="0.01" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="sinking_fund" class="block text-sm font-medium text-gray-700">Sinking Fund</label>
                            <input type="number" name="sinking_fund" id="sinking_fund" step="0.01" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="manual_quarter" class="block text-sm font-medium text-gray-700">Quarter</label>
                            <select name="quarter" id="manual_quarter" required 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="1">Q1 (Jan-Mar)</option>
                                <option value="2">Q2 (Apr-Jun)</option>
                                <option value="3">Q3 (Jul-Sep)</option>
                                <option value="4">Q4 (Oct-Dec)</option>
                            </select>
                        </div>
                        <div>
                            <label for="manual_year" class="block text-sm font-medium text-gray-700">Year</label>
                            <select name="year" id="manual_year" required 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @for ($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Any additional notes..."></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeManualEntryModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Create Manual Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openGenerateModal() {
            document.getElementById('generateModal').classList.remove('hidden');
        }
        
        function closeGenerateModal() {
            document.getElementById('generateModal').classList.add('hidden');
        }
        
        function openManualEntryModal() {
            document.getElementById('manualEntryModal').classList.remove('hidden');
        }
        
        function closeManualEntryModal() {
            document.getElementById('manualEntryModal').classList.add('hidden');
        }

        function updateApartmentArea() {
            const select = document.getElementById('apartment_id');
            const areaDisplay = document.getElementById('areaDisplay');
            const apartmentArea = document.getElementById('apartmentArea');

            if (select.value) {
                const selectedOption = select.options[select.selectedIndex];
                const area = selectedOption.getAttribute('data-area');
                
                apartmentArea.textContent = `${area} sq ft`;
                areaDisplay.style.display = 'block';
            } else {
                areaDisplay.style.display = 'none';
            }
        }
        
        function markAsPaid(invoiceId) {
            if (confirm('Mark this invoice as paid?')) {
                fetch(`/management-fees/invoice/${invoiceId}/mark-paid`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to mark invoice as paid');
                });
            }
        }
    </script>
</x-app-layout>
