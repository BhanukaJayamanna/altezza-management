<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Lease') }}
            </h2>
            <a href="{{ route('leases.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Leases
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <x-card>
                <form method="POST" action="{{ route('leases.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Basic Lease Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Lease Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Apartment -->
                            <div>
                                <label for="apartment_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Apartment <span class="text-red-500">*</span>
                                </label>
                                <select id="apartment_id" 
                                        name="apartment_id" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Apartment</option>
                                    @foreach($apartments as $apartment)
                                        <option value="{{ $apartment->id }}" {{ old('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                            {{ $apartment->number }} - {{ $apartment->type }} ({{ $apartment->block }}) - ${{ number_format($apartment->rent_amount, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tenant -->
                            <div>
                                <label for="tenant_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tenant <span class="text-red-500">*</span>
                                </label>
                                <select id="tenant_id" 
                                        name="tenant_id" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Tenant</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ (old('tenant_id') == $tenant->id || (isset($selectedTenantId) && $selectedTenantId == $tenant->id)) ? 'selected' : '' }}>
                                            {{ $tenant->name }} ({{ $tenant->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Owner -->
                            <div>
                                <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Property Owner <span class="text-red-500">*</span>
                                </label>
                                <select id="owner_id" 
                                        name="owner_id" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Owner</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}" {{ old('owner_id') == $owner->id ? 'selected' : '' }}>
                                            {{ $owner->name }} ({{ $owner->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select id="status" 
                                        name="status" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="terminated" {{ old('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Lease Duration -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Lease Duration</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Start Date -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Start Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       id="start_date" 
                                       name="start_date" 
                                       value="{{ old('start_date') }}" 
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    End Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       id="end_date" 
                                       name="end_date" 
                                       value="{{ old('end_date') }}" 
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Maintenance Charge -->
                            <div>
                                <label for="maintenance_charge" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maintenance Charge
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" 
                                           id="maintenance_charge" 
                                           name="maintenance_charge" 
                                           value="{{ old('maintenance_charge', 0) }}" 
                                           step="0.01" 
                                           min="0"
                                           class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Monthly Rent -->
                            <div>
                                <label for="rent_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Monthly Rent <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" 
                                           id="rent_amount" 
                                           name="rent_amount" 
                                           value="{{ old('rent_amount') }}" 
                                           step="0.01" 
                                           min="0" 
                                           required
                                           class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Security Deposit -->
                            <div>
                                <label for="security_deposit" class="block text-sm font-medium text-gray-700 mb-2">
                                    Security Deposit
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" 
                                           id="security_deposit" 
                                           name="security_deposit" 
                                           value="{{ old('security_deposit') }}" 
                                           step="0.01" 
                                           min="0"
                                           class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lease Terms -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Lease Terms</h3>
                        <p class="text-sm text-gray-600 mb-4">Add specific terms and conditions for this lease.</p>
                        
                        <div id="lease-terms" class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <input type="text" 
                                       name="terms_conditions[]" 
                                       value="{{ old('terms_conditions.0') }}"
                                       placeholder="Enter lease term (e.g., No pets allowed)"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <button type="button" onclick="addLeaseTerm()" class="px-3 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Contract File -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contract Document</h3>
                        <p class="text-sm text-gray-600 mb-4">Upload the signed lease contract (optional).</p>
                        
                        <div>
                            <label for="contract_file" class="block text-sm font-medium text-gray-700 mb-2">
                                Contract File
                            </label>
                            <input type="file" 
                                   id="contract_file" 
                                   name="contract_file" 
                                   accept=".pdf,.doc,.docx"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Accepted formats: PDF, DOC, DOCX (Max: 5MB)</p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('leases.index') }}" 
                           class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <x-button type="submit">
                            Create Lease
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    <script>
        function addLeaseTerm() {
            const container = document.getElementById('lease-terms');
            const termDiv = document.createElement('div');
            termDiv.className = 'flex items-center space-x-3';
            termDiv.innerHTML = `
                <input type="text" 
                       name="terms_conditions[]" 
                       placeholder="Enter lease term"
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="button" onclick="removeLeaseTerm(this)" class="px-3 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(termDiv);
        }

        function removeLeaseTerm(button) {
            button.parentElement.remove();
        }

        // Auto-populate rent from apartment selection
        document.getElementById('apartment_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const rentMatch = selectedOption.text.match(/\$([0-9,]+\.?\d*)/);
                if (rentMatch) {
                    const rent = rentMatch[1].replace(',', '');
                    document.getElementById('rent_amount').value = rent;
                }
            }
        });

        // Clean up empty terms before form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const termInputs = document.querySelectorAll('input[name="terms_conditions[]"]');
            termInputs.forEach(function(input) {
                if (input.value.trim() === '') {
                    input.remove();
                }
            });
        });
    </script>
</x-app-layout>
