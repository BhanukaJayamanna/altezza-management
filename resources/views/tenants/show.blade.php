<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tenant Details') }} - {{ $tenant->name }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('tenants.edit', $tenant) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Edit Tenant
                </a>
                <a href="{{ route('tenants.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Tenants
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Personal Information -->
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Full Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $tenant->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $tenant->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $tenant->phone ?? 'N/A' }}</p>
                        </div>
                        
                        @if($tenant->tenantProfile)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">ID Document</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $tenant->tenantProfile->id_document ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                @if($tenant->tenantProfile->status === 'active') bg-green-100 text-green-800
                                @elseif($tenant->tenantProfile->status === 'inactive') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($tenant->tenantProfile->status) }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Member Since</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $tenant->created_at->format('F j, Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </x-card>

            <!-- Apartment & Lease Information -->
            @if($tenant->tenantProfile)
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Apartment & Lease Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Current Apartment</label>
                            @if($tenant->tenantProfile->apartment)
                                <div class="mt-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $tenant->tenantProfile->apartment->number }}</p>
                                    <p class="text-xs text-gray-500">{{ $tenant->tenantProfile->apartment->type }} - {{ $tenant->tenantProfile->apartment->block }}</p>
                                    <p class="text-xs text-gray-500">Rent: @currency($tenant->tenantProfile->apartment->rent_amount)</p>
                                </div>
                            @else
                                <p class="mt-1 text-sm text-gray-500">No apartment assigned</p>
                            @endif
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Lease Start Date</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $tenant->tenantProfile->lease_start ? $tenant->tenantProfile->lease_start->format('F j, Y') : 'N/A' }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Lease End Date</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $tenant->tenantProfile->lease_end ? $tenant->tenantProfile->lease_end->format('F j, Y') : 'N/A' }}
                            </p>
                        </div>
                        
                        @if($tenant->tenantProfile->lease_start && $tenant->tenantProfile->lease_end)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Lease Duration</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $tenant->tenantProfile->lease_duration }} months
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Lease Status</label>
                            @if($tenant->tenantProfile->isLeaseExpired())
                                <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Expired
                                </span>
                            @elseif($tenant->tenantProfile->isLeaseExpiring())
                                <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Expiring Soon
                                </span>
                            @else
                                <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </x-card>
            @endif

            <!-- Emergency Contact Information -->
            @if($tenant->tenantProfile && ($tenant->tenantProfile->emergency_contact || $tenant->tenantProfile->emergency_phone))
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Emergency Contact Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Emergency Contact Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $tenant->tenantProfile->emergency_contact ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Emergency Contact Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $tenant->tenantProfile->emergency_phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </x-card>
            @endif

            <!-- Additional Notes -->
            @if($tenant->tenantProfile && $tenant->tenantProfile->notes)
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Additional Notes</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $tenant->tenantProfile->notes }}</p>
                </div>
            </x-card>
            @endif

            <!-- Formal Leases -->
            @if($tenant->leases && $tenant->leases->count() > 0)
            <x-card id="leases">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Lease Agreements</h3>
                        <a href="{{ route('leases.create') }}?tenant_id={{ $tenant->id }}" 
                           class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Add New Lease
                        </a>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        @foreach($tenant->leases as $lease)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">{{ $lease->lease_number }}</h4>
                                    <p class="text-sm text-gray-500">{{ $lease->apartment->number ?? 'N/A' }} - {{ $lease->apartment->type ?? '' }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($lease->status === 'active') bg-green-100 text-green-800
                                        @elseif($lease->status === 'expired') bg-red-100 text-red-800
                                        @elseif($lease->status === 'terminated') bg-gray-100 text-gray-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($lease->status) }}
                                    </span>
                                    <a href="{{ route('leases.show', $lease) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">View Details</a>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Start Date</label>
                                    <p class="text-gray-900">{{ $lease->start_date->format('M j, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">End Date</label>
                                    <p class="text-gray-900">{{ $lease->end_date->format('M j, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Rent Amount</label>
                                    <p class="text-gray-900">@currency($lease->rent_amount)</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Security Deposit</label>
                                    <p class="text-gray-900">@currency($lease->security_deposit)</p>
                                </div>
                            </div>
                            
                            @if($lease->owner)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <p class="text-xs text-gray-500">Owner: <span class="text-gray-900">{{ $lease->owner->name }}</span></p>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </x-card>
            @else
            <x-card id="leases">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Lease Agreements</h3>
                </div>
                <div class="px-6 py-4 text-center">
                    <p class="text-gray-500 mb-4">No formal lease agreements found for this tenant.</p>
                    <a href="{{ route('leases.create') }}?tenant_id={{ $tenant->id }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Create First Lease
                    </a>
                </div>
            </x-card>
            @endif

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Invoices -->
                <x-card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Invoices</h3>
                    </div>
                    <div class="px-6 py-4">
                        <!-- This would show recent invoices for the tenant -->
                        <p class="text-sm text-gray-500">Invoice history will be displayed here</p>
                    </div>
                </x-card>

                <!-- Recent Payments -->
                <x-card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                    </div>
                    <div class="px-6 py-4">
                        <!-- This would show recent payments from the tenant -->
                        <p class="text-sm text-gray-500">Payment history will be displayed here</p>
                    </div>
                </x-card>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6">
                <div class="flex space-x-3">
                    <a href="{{ route('invoices.create') }}?tenant_id={{ $tenant->id }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        Create Invoice
                    </a>
                    @if($tenant->tenantProfile && $tenant->tenantProfile->apartment)
                    <a href="{{ route('apartments.show', $tenant->tenantProfile->apartment) }}" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                        View Apartment
                    </a>
                    @endif
                </div>
                
                <form method="POST" action="{{ route('tenants.destroy', $tenant) }}" 
                      onsubmit="return confirm('Are you sure you want to delete this tenant? This action cannot be undone.')"
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Delete Tenant
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
