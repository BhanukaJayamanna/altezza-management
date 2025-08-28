<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Apartment') }} - {{ $apartment->number }}
            </h2>
            <a href="{{ route('apartments.show', $apartment) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Details
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('apartments.update', $apartment) }}">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Apartment Number -->
                                <div>
                                    <label for="number" class="block font-medium text-sm text-gray-700 mb-2">
                                        Apartment Number <span class="text-red-500">*</span>
                                    </label>
                                    <input id="number" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('number') border-red-500 @enderror" 
                                           type="text" name="number" value="{{ old('number', $apartment->number) }}" required autofocus />
                                    @error('number')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Type -->
                                <div>
                                    <label for="type" class="block font-medium text-sm text-gray-700 mb-2">
                                        Type <span class="text-red-500">*</span>
                                    </label>
                                    <select id="type" name="type" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('type') border-red-500 @enderror" required>
                                        <option value="">Select Type</option>
                                        <option value="1bhk" {{ old('type', $apartment->type) == '1bhk' ? 'selected' : '' }}>1 BHK</option>
                                        <option value="2bhk" {{ old('type', $apartment->type) == '2bhk' ? 'selected' : '' }}>2 BHK</option>
                                        <option value="3bhk" {{ old('type', $apartment->type) == '3bhk' ? 'selected' : '' }}>3 BHK</option>
                                        <option value="4bhk" {{ old('type', $apartment->type) == '4bhk' ? 'selected' : '' }}>4 BHK</option>
                                        <option value="studio" {{ old('type', $apartment->type) == 'studio' ? 'selected' : '' }}>Studio</option>
                                        <option value="penthouse" {{ old('type', $apartment->type) == 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                                    </select>
                                    @error('type')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Assessment No -->
                                <div>
                                    <label for="assessment_no" class="block font-medium text-sm text-gray-700 mb-2">
                                        Assessment No
                                    </label>
                                    <input id="assessment_no" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('assessment_no') border-red-500 @enderror" 
                                           type="text" name="assessment_no" value="{{ old('assessment_no', $apartment->assessment_no) }}" />
                                    @error('assessment_no')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Area -->
                                <div>
                                    <label for="area" class="block font-medium text-sm text-gray-700 mb-2">
                                        Area (sq ft)
                                    </label>
                                    <input id="area" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('area') border-red-500 @enderror" 
                                           type="number" name="area" value="{{ old('area', $apartment->area) }}" min="1" step="0.01" />
                                    @error('area')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status" class="block font-medium text-sm text-gray-700 mb-2">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-red-500 @enderror" required>
                                        <option value="">Select Status</option>
                                        <option value="vacant" {{ old('status', $apartment->status) == 'vacant' ? 'selected' : '' }}>Vacant</option>
                                        <option value="occupied" {{ old('status', $apartment->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                        <option value="maintenance" {{ old('status', $apartment->status) == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mt-6">
                                <label for="description" class="block font-medium text-sm text-gray-700 mb-2">
                                    Description
                                </label>
                                <textarea id="description" name="description" rows="3" 
                                          class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror"
                                          placeholder="Enter apartment description...">{{ old('description', $apartment->description) }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Ownership & Rental Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ownership & Rental Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Management Corporation -->
                                <div>
                                    <label for="management_corporation_id" class="block font-medium text-sm text-gray-700 mb-2">
                                        Management Corporation <span class="text-red-500">*</span>
                                    </label>
                                    <select id="management_corporation_id" name="management_corporation_id" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('management_corporation_id') border-red-500 @enderror">
                                        <option value="">Select Management Corporation</option>
                                        @foreach($managementCorporations as $corp)
                                            <option value="{{ $corp->id }}" {{ old('management_corporation_id', $apartment->management_corporation_id) == $corp->id ? 'selected' : '' }}>
                                                {{ $corp->name }} ({{ $corp->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('management_corporation_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Owner -->
                                <div>
                                    <label for "owner_id" class="block font-medium text-sm text-gray-700 mb-2">
                                        Current Owner
                                    </label>
                                    <select id="owner_id" name="owner_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('owner_id') border-red-500 @enderror">
                                        <option value="">Select Owner</option>
                                        @foreach($owners as $owner)
                                            <option value="{{ $owner->id }}" {{ old('owner_id', $apartment->owner_id) == $owner->id ? 'selected' : '' }}>
                                                @if($owner->user)
                                                    {{ $owner->user->name }} ({{ $owner->user->email }})
                                                @else
                                                    Owner #{{ $owner->id }} (No user linked)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('owner_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Rent Amount -->
                                <div>
                                    <label for="rent_amount" class="block font-medium text-sm text-gray-700 mb-2">
                                        Monthly Rent (LKR)
                                    </label>
                                    <input id="rent_amount" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('rent_amount') border-red-500 @enderror" 
                                           type="number" name="rent_amount" value="{{ old('rent_amount', $apartment->rent_amount) }}" min="0" step="0.01" />
                                    @error('rent_amount')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Security Deposit -->
                                <div>
                                    <label for="security_deposit" class="block font-medium text-sm text-gray-700 mb-2">
                                        Security Deposit ($)
                                    </label>
                                    <input id="security_deposit" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('security_deposit') border-red-500 @enderror" 
                                           type="number" name="security_deposit" value="{{ old('security_deposit', $apartment->security_deposit) }}" min="0" step="0.01" />
                                    @error('security_deposit')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex items-center space-x-4">
                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update Apartment
                                </button>
                                
                                <a href="{{ route('apartments.show', $apartment) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancel
                                </a>
                            </div>

                            <!-- Delete Button - Separate Form -->
                            <div>
                                <!-- Delete button will be outside the update form -->
                            </div>
                        </div>
                    </form>
                    
                    <!-- Delete Form - Outside the Update Form -->
                    <div class="mt-4 pt-4 border-t border-gray-200 flex justify-end">
                        <form method="POST" action="{{ route('apartments.destroy', $apartment) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this apartment? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Apartment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
