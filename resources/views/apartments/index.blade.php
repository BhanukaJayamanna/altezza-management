<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Apartments') }}
            </h2>
            <a href="{{ route('apartments.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Apartment
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <x-breadcrumb :items="[
                ['name' => 'Apartments']
            ]" />

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('apartments.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Search Number</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Apartment number...">
                        </div>
                        
                        <div>
                            <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="type" class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                            <select name="type" id="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Types</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                        {{ strtoupper($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="assessment_no" class="block text-xs font-medium text-gray-700 mb-1">Assessment No</label>
                            <select name="assessment_no" id="assessment_no" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Assessment Numbers</option>
                                @foreach($assessment_nos as $assessment_no)
                                    <option value="{{ $assessment_no }}" {{ request('assessment_no') == $assessment_no ? 'selected' : '' }}>
                                        {{ $assessment_no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Filter
                            </button>
                            <a href="{{ route('apartments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Apartments Grid -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($apartments->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($apartments as $apartment)
                                <div class="group relative bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                                    <!-- Status Badge -->
                                    <div class="absolute top-3 right-3 z-10">
                                        <span class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-semibold shadow-sm
                                            @if($apartment->status === 'vacant') bg-emerald-50 text-emerald-700 border border-emerald-200
                                            @elseif($apartment->status === 'occupied') bg-blue-50 text-blue-700 border border-blue-200
                                            @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                                @if($apartment->status === 'vacant') bg-emerald-400
                                                @elseif($apartment->status === 'occupied') bg-blue-400
                                                @else bg-amber-400 @endif"></span>
                                            {{ ucfirst($apartment->status) }}
                                        </span>
                                    </div>

                                    <!-- Card Header -->
                                    <div class="p-6 pb-4 pr-24">
                                        <div class="flex items-start justify-between mb-4">
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                                    Apartment {{ $apartment->number }}
                                                </h3>
                                                @if($apartment->assessment_no)
                                                    <p class="text-sm text-gray-500 mt-1">Assessment No: {{ $apartment->assessment_no }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Property Details -->
                                        <div class="space-y-3">
                                            <!-- Type -->
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                                <span class="font-medium">{{ strtoupper($apartment->type) }}</span>
                                            </div>

                                            <!-- Area -->
                                            @if($apartment->area)
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                                    </svg>
                                                    <span>{{ number_format($apartment->area) }} sq ft</span>
                                                </div>
                                            @endif

                                            <!-- Rent -->
                                            @if($apartment->rent_amount)
                                                <div class="flex items-center justify-between">
                                                    <span class="text-2xl font-bold text-gray-900">@currency($apartment->rent_amount)</span>
                                                    <span class="text-sm text-gray-500">/month</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- People Info -->
                                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                                        <div class="space-y-2">
                                            <!-- Management Corporation -->
                                            <div class="flex items-center text-sm">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                                <span class="text-gray-500">Management Corp:</span>
                                                <span class="ml-1 font-medium text-gray-900">{{ $apartment->managementCorporation->name ?? 'N/A' }}</span>
                                            </div>
                                            
                                            <!-- Owner -->
                                            @if($apartment->currentOwner && $apartment->currentOwner->user)
                                                <div class="flex items-center text-sm">
                                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                    <span class="text-gray-500">Owner:</span>
                                                    <span class="ml-1 font-medium text-gray-900">{{ $apartment->currentOwner->user->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="px-6 py-4 bg-white border-t border-gray-100">
                                        <div class="flex items-center justify-between">
                                            <a href="{{ route('apartments.show', $apartment) }}" 
                                               class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                View Details
                                            </a>
                                            
                                            <div class="flex items-center space-x-3">
                                                <a href="{{ route('apartments.edit', $apartment) }}" 
                                                   class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    Edit
                                                </a>
                                                
                                                <form method="POST" action="{{ route('apartments.destroy', $apartment) }}" class="inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this apartment?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-800 transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $apartments->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8s-16-3.582-16-8"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No apartments found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new apartment.</p>
                            <div class="mt-6">
                                <a href="{{ route('apartments.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add New Apartment
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
