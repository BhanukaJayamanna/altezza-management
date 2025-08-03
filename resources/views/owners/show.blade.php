<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Owner Details') }} - {{ $owner->name }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('owners.edit', $owner) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Edit Owner
                </a>
                <a href="{{ route('owners.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Owners
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
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->phone ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">ID Document</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->id_document ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                @if($owner->status === 'active') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($owner->status) }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Registered Since</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->created_at->format('F j, Y') }}</p>
                        </div>
                    </div>

                    @if($owner->address)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500">Address</label>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $owner->address }}</p>
                    </div>
                    @endif
                </div>
            </x-card>

            <!-- Bank Details -->
            @if($owner->bank_details)
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Bank Account Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Account Holder Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->bank_details['account_name'] ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Account Number</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if(isset($owner->bank_details['account_number']))
                                    {{ str_repeat('*', max(0, strlen($owner->bank_details['account_number']) - 4)) . substr($owner->bank_details['account_number'], -4) }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Bank Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->bank_details['bank_name'] ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Routing Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->bank_details['routing_number'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </x-card>
            @endif

            <!-- Property Portfolio Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <x-stat-card 
                    title="Total Properties"
                    :value="$owner->apartments->count()"
                    icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                    color="blue" />

                <x-stat-card 
                    title="Occupied Units"
                    :value="$owner->apartments->where('status', 'occupied')->count()"
                    icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                    color="green" />

                <x-stat-card 
                    title="Vacant Units"
                    :value="$owner->apartments->where('status', 'vacant')->count()"
                    icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                    color="yellow" />

                <x-stat-card 
                    title="Monthly Revenue"
                    :value="'$' . number_format($owner->apartments->where('status', 'occupied')->sum('rent_amount'), 2)"
                    icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    color="purple" />
            </div>

            <!-- Properties List -->
            @if($owner->apartments->count() > 0)
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Properties ({{ $owner->apartments->count() }})</h3>
                </div>
                <div class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Apartment
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Type & Area
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Rent Amount
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Current Tenant
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($owner->apartments as $apartment)
                                    <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-blue-50 transition-all duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900">{{ $apartment->number }}</div>
                                            <div class="text-sm text-gray-500">Block {{ $apartment->block }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ ucfirst($apartment->type) }}</div>
                                            <div class="text-sm text-gray-500">{{ $apartment->area }} sq ft</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">@currency($apartment->rent_amount)</div>
                                            <div class="text-sm text-gray-500">per month</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                                                @if($apartment->status === 'occupied') bg-green-100 text-green-800
                                                @elseif($apartment->status === 'vacant') bg-blue-100 text-blue-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($apartment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($apartment->tenant)
                                                <div class="text-sm font-medium text-gray-900">{{ $apartment->tenant->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $apartment->tenant->email }}</div>
                                            @else
                                                <span class="text-sm text-gray-400">No tenant</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('apartments.show', $apartment) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-card>
            @endif

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6">
                <div class="flex space-x-3">
                    <a href="{{ route('apartments.create') }}?owner_id={{ $owner->id }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        Add Property
                    </a>
                </div>
                
                <form method="POST" action="{{ route('owners.destroy', $owner) }}" 
                      onsubmit="return confirm('Are you sure you want to delete this owner? This action cannot be undone.')"
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Delete Owner
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
