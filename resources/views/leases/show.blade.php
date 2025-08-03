<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Lease Details') }} - {{ $lease->lease_number }}
            </h2>
            <div class="flex space-x-2">
                @if($lease->status === 'active' && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
                    <a href="{{ route('leases.edit', $lease) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Lease
                    </a>
                    
                    <form action="{{ route('leases.terminate', $lease) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirm('Are you sure you want to terminate this lease?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Terminate
                        </button>
                    </form>
                @endif

                <a href="{{ route('leases.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Leases
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
                <!-- Main Lease Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <!-- Lease Header -->
                            <div class="border-b border-gray-200 pb-6 mb-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">{{ $lease->lease_number }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($lease->status === 'active') bg-green-100 text-green-800
                                                @elseif($lease->status === 'expired') bg-red-100 text-red-800
                                                @elseif($lease->status === 'terminated') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($lease->status) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-gray-900">
                                            @currency($lease->rent_amount)/month
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Created: {{ $lease->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Lease Period -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Start Date</h4>
                                    <p class="text-lg font-semibold text-gray-700">{{ $lease->start_date->format('M d, Y') }}</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">End Date</h4>
                                    <p class="text-lg font-semibold text-gray-700">{{ $lease->end_date->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <!-- Financial Details -->
                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-3">Financial Details</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-gray-700">Monthly Rent</h5>
                                        <p class="text-xl font-bold text-blue-600">@currency($lease->rent_amount)</p>
                                    </div>
                                    <div class="bg-green-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-gray-700">Security Deposit</h5>
                                        <p class="text-xl font-bold text-green-600">@currency($lease->security_deposit)</p>
                                    </div>
                                    <div class="bg-yellow-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-gray-700">Maintenance Charge</h5>
                                        <p class="text-xl font-bold text-yellow-600">@currency($lease->maintenance_charge)</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            @if($lease->terms_conditions && count($lease->terms_conditions) > 0)
                                <div class="mb-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Terms & Conditions</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <ul class="space-y-2">
                                            @foreach($lease->terms_conditions as $term)
                                                <li class="flex items-start">
                                                    <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-sm text-gray-700">{{ $term }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Contract File -->
                    @if($lease->contract_file)
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Contract Document</h3>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Contract.pdf</p>
                                            <p class="text-xs text-gray-500">Click to download</p>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $lease->contract_file) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Tenant Information -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Tenant Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Name</p>
                                    <p class="text-sm text-gray-900">{{ $lease->tenant->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <p class="text-sm text-gray-900">{{ $lease->tenant->email }}</p>
                                </div>
                                @if($lease->tenant->phone)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Phone</p>
                                        <p class="text-sm text-gray-900">{{ $lease->tenant->phone }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Apartment Information -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Apartment Details</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Unit Number</p>
                                    <p class="text-sm text-gray-900">{{ $lease->apartment->number }}</p>
                                </div>
                                @if($lease->apartment->block)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Block</p>
                                        <p class="text-sm text-gray-900">{{ $lease->apartment->block }}</p>
                                    </div>
                                @endif
                                @if($lease->apartment->floor)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Floor</p>
                                        <p class="text-sm text-gray-900">{{ $lease->apartment->floor }}</p>
                                    </div>
                                @endif
                                @if($lease->apartment->type)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Type</p>
                                        <p class="text-sm text-gray-900">{{ ucfirst($lease->apartment->type) }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Owner Information -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Owner Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Name</p>
                                    <p class="text-sm text-gray-900">{{ $lease->owner->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <p class="text-sm text-gray-900">{{ $lease->owner->email }}</p>
                                </div>
                                @if($lease->owner->phone)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Phone</p>
                                        <p class="text-sm text-gray-900">{{ $lease->owner->phone }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
