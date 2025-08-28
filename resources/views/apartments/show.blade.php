<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Apartment Details') }} - {{ $apartment->full_address }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('apartments.edit', $apartment) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('apartments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Basic Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apartment Number</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $apartment->number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($apartment->status === 'vacant') bg-green-100 text-green-800
                                @elseif($apartment->status === 'occupied') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($apartment->status) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <p class="text-gray-900">{{ strtoupper($apartment->type) }}</p>
                        </div>
                        @if($apartment->assessment_no)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assessment No</label>
                            <p class="text-gray-900">{{ $apartment->assessment_no }}</p>
                        </div>
                        @endif
                        @if($apartment->area)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Area</label>
                            <p class="text-gray-900">{{ number_format($apartment->area) }} sq ft</p>
                        </div>
                        @endif
                        @if($apartment->rent_amount)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Rent</label>
                            <p class="text-lg font-semibold text-green-600">@currency($apartment->rent_amount)</p>
                        </div>
                        @endif
                    </div>
                    @if($apartment->description)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <p class="text-gray-900">{{ $apartment->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Management Corporation Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Management Corporation</h3>
                        @if($apartment->managementCorporation)
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Corporation Name</label>
                                <p class="text-gray-900">{{ $apartment->managementCorporation->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <p class="text-gray-900">{{ $apartment->managementCorporation->email }}</p>
                            </div>
                            @if($apartment->managementCorporation->phone)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <p class="text-gray-900">{{ $apartment->managementCorporation->phone }}</p>
                            </div>
                            @endif
                        </div>
                        @else
                        <p class="text-gray-500">No management corporation assigned</p>
                        @endif
                    </div>
                </div>

                <!-- Current Owner Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Owner</h3>
                        @if($apartment->currentOwner && $apartment->currentOwner->user)
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <p class="text-gray-900">{{ $apartment->currentOwner->user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <p class="text-gray-900">{{ $apartment->currentOwner->user->email }}</p>
                            </div>
                            @if($apartment->currentOwner->user->phone)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <p class="text-gray-900">{{ $apartment->currentOwner->user->phone }}</p>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="text-center py-4">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No owner assigned</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Invoices -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Invoices</h3>
                        @if($apartment->invoices->count() > 0)
                        <div class="space-y-3">
                            @foreach($apartment->invoices->take(5) as $invoice)
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $invoice->type }} - {{ $invoice->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">@currency($invoice->total_amount)</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($invoice->status === 'paid') bg-green-100 text-green-800
                                        @elseif($invoice->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500 text-sm">No invoices yet</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Maintenance Requests -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Maintenance</h3>
                        @if($apartment->maintenanceRequests->count() > 0)
                        <div class="space-y-3">
                            @foreach($apartment->maintenanceRequests->take(5) as $request)
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $request->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $request->created_at->format('M d, Y') }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($request->status === 'completed') bg-green-100 text-green-800
                                    @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($request->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500 text-sm">No maintenance requests</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
