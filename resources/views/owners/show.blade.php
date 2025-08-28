<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Owner Details') }} - {{ $owner->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('owners.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Owners
                </a>
                <a href="{{ route('owners.edit', $owner) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Owner
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Success/Error Messages -->
            <x-alerts />

            <!-- Owner Information Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Personal Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Full Name</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $owner->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->email }}</p>
                        </div>
                        @if($owner->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->phone }}</p>
                        </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Account Status</label>
                            @if($owner->ownerProfile)
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                                    @if($owner->ownerProfile->status === 'active') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($owner->ownerProfile->status ?? 'Unknown') }}
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    No Profile
                                </span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Member Since</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->created_at->format('M d, Y') }}</p>
                        </div>
                        @if($owner->ownerProfile && $owner->ownerProfile->id_document)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">ID Document</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->ownerProfile->id_document }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Property Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Property Information
                    </h3>
                </div>
                <div class="p-6">
                    @if($apartment)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Apartment Number</label>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $apartment->number }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Type</label>
                                <p class="mt-1 text-sm text-gray-900">{{ strtoupper($apartment->type) }}</p>
                            </div>
                            @if($apartment->assessment_no)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Assessment No</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $apartment->assessment_no }}</p>
                            </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                                    @if($apartment->status === 'occupied') bg-green-100 text-green-800
                                    @elseif($apartment->status === 'vacant') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($apartment->status) }}
                                </span>
                            </div>
                            @if($apartment->area)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Area</label>
                                <p class="mt-1 text-sm text-gray-900">{{ number_format($apartment->area, 2) }} sq ft</p>
                            </div>
                            @endif
                            @if($apartment->rent_amount)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Monthly Rent</label>
                                <p class="mt-1 text-lg font-semibold text-gray-900">${{ number_format($apartment->rent_amount, 2) }}</p>
                            </div>
                            @endif
                            @if($apartment->managementCorporation)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Management Corporation</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $apartment->managementCorporation->name }}</p>
                            </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Actions</label>
                                <a href="{{ route('apartments.show', $apartment) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700">
                                    View Details
                                </a>
                            </div>
                        </div>
                        @if($apartment->description)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500">Property Description</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $apartment->description }}</p>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Property Assigned</h3>
                            <p class="mt-1 text-sm text-gray-500">This owner is not currently assigned to any apartment.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Emergency Contact Information -->
            @if($owner->ownerProfile && ($owner->ownerProfile->emergency_contact || $owner->ownerProfile->emergency_phone))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        Emergency Contact
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($owner->ownerProfile->emergency_contact)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Contact Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->ownerProfile->emergency_contact }}</p>
                        </div>
                        @endif
                        @if($owner->ownerProfile->emergency_phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Contact Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $owner->ownerProfile->emergency_phone }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Activity (if apartment exists) -->
            @if($apartment)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Invoices -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Recent Invoices
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($recentInvoices->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentInvoices as $invoice)
                                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Invoice #{{ $invoice->id }}</p>
                                        <p class="text-xs text-gray-500">{{ $invoice->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-900">${{ number_format($invoice->amount ?? 0, 2) }}</p>
                                        <span class="inline-flex px-2 py-1 text-xs rounded-full 
                                            @if($invoice->status === 'paid') bg-green-100 text-green-800
                                            @elseif($invoice->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($invoice->status ?? 'pending') }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center py-4">No recent invoices found.</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Maintenance Requests -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Maintenance Requests
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($maintenanceRequests->count() > 0)
                            <div class="space-y-3">
                                @foreach($maintenanceRequests as $request)
                                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ Str::limit($request->title ?? 'Maintenance Request', 30) }}</p>
                                        <p class="text-xs text-gray-500">{{ $request->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <span class="inline-flex px-2 py-1 text-xs rounded-full 
                                            @if($request->status === 'completed') bg-green-100 text-green-800
                                            @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                                            @elseif($request->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $request->status ?? 'pending')) }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center py-4">No maintenance requests found.</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Additional Notes -->
            @if($owner->ownerProfile && $owner->ownerProfile->notes)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Notes
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $owner->ownerProfile->notes }}</p>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
