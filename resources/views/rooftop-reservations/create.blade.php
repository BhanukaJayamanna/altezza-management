<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Rooftop Reservation') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Breadcrumb -->
                    <x-breadcrumb :items="[
                        ['name' => 'Dashboard', 'url' => route('dashboard')],
                        ['name' => 'Rooftop Reservations', 'url' => route('rooftop-reservations.index')],
                        ['name' => 'Create', 'url' => null]
                    ]" />

                    <form method="POST" action="{{ route('rooftop-reservations.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Owner Selection -->
                                <div>
                                    <label for="owner_id" class="block text-sm font-medium text-gray-700">Owner *</label>
                                    <select name="owner_id" id="owner_id" required 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select a owner</option>
                                        @foreach($owners as $owner)
                                            <option value="{{ $owner->id }}" {{ old('owner_id') == $owner->id ? 'selected' : '' }}>
                                                {{ $owner->name }} - {{ $owner->apartment->unit_number ?? 'No Apartment' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('owner_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Event Title -->
                                <div>
                                    <label for="event_title" class="block text-sm font-medium text-gray-700">Event Title *</label>
                                    <input type="text" name="event_title" id="event_title" value="{{ old('event_title') }}" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                           placeholder="Birthday Party, Wedding, etc.">
                                    @error('event_title')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Reservation Date -->
                                <div>
                                    <label for="reservation_date" class="block text-sm font-medium text-gray-700">Reservation Date *</label>
                                    <input type="date" name="reservation_date" id="reservation_date" value="{{ old('reservation_date') }}" required
                                           min="{{ date('Y-m-d') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('reservation_date')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Time Selection -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time *</label>
                                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" required
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('start_time')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="end_time" class="block text-sm font-medium text-gray-700">End Time *</label>
                                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" required
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('end_time')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Number of Guests -->
                                <div>
                                    <label for="number_of_guests" class="block text-sm font-medium text-gray-700">Number of Guests *</label>
                                    <input type="number" name="number_of_guests" id="number_of_guests" value="{{ old('number_of_guests') }}" required
                                           min="1" max="200"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                           placeholder="Enter number of guests">
                                    @error('number_of_guests')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Event Type -->
                                <div>
                                    <label for="event_type" class="block text-sm font-medium text-gray-700">Event Type *</label>
                                    <select name="event_type" id="event_type" required 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select event type</option>
                                        <option value="birthday" {{ old('event_type') == 'birthday' ? 'selected' : '' }}>Birthday Party</option>
                                        <option value="wedding" {{ old('event_type') == 'wedding' ? 'selected' : '' }}>Wedding</option>
                                        <option value="corporate" {{ old('event_type') == 'corporate' ? 'selected' : '' }}>Corporate Event</option>
                                        <option value="family_gathering" {{ old('event_type') == 'family_gathering' ? 'selected' : '' }}>Family Gathering</option>
                                        <option value="party" {{ old('event_type') == 'party' ? 'selected' : '' }}>General Party</option>
                                        <option value="other" {{ old('event_type') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('event_type')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Special Requirements -->
                                <div>
                                    <label for="special_requirements" class="block text-sm font-medium text-gray-700">Special Requirements</label>
                                    <textarea name="special_requirements" id="special_requirements" rows="4"
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                              placeholder="Any special requirements or notes...">{{ old('special_requirements') }}</textarea>
                                    @error('special_requirements')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Pricing Information -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Pricing Information</h4>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex justify-between">
                                            <span>Base Rate (per hour):</span>
                                            <span class="font-medium">LKR {{ number_format(setting('rooftop_base_rate_per_hour', 500)) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Guest Charge (per person):</span>
                                            <span class="font-medium">LKR {{ number_format(setting('rooftop_guest_charge_per_person', 50)) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Security Deposit:</span>
                                            <span class="font-medium">LKR {{ number_format(setting('rooftop_security_deposit', 2000)) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Cleaning Charge:</span>
                                            <span class="font-medium">LKR {{ number_format(setting('rooftop_cleaning_charge', 500)) }}</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="flex justify-between font-semibold">
                                            <span>Total (estimated):</span>
                                            <span id="estimated-total">LKR 0</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terms and Conditions -->
                                <div>
                                    <div class="flex items-start">
                                        <input type="checkbox" name="terms_accepted" id="terms_accepted" required
                                               class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="terms_accepted" class="ml-2 text-sm text-gray-700">
                                            I agree to the 
                                            <a href="#" class="text-indigo-600 hover:text-indigo-500 underline" onclick="showTermsModal()">
                                                terms and conditions
                                            </a> for rooftop usage *
                                        </label>
                                    </div>
                                    @error('terms_accepted')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('rooftop-reservations.index') }}" 
                               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Create Reservation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div id="termsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Rooftop Reservation Terms & Conditions</h3>
                    <button onclick="hideTermsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="text-sm text-gray-700 space-y-3 max-h-96 overflow-y-auto">
                    <p><strong>1. Reservation Policy:</strong></p>
                    <ul class="list-disc pl-6 space-y-1">
                        <li>Reservations must be made at least {{ setting('rooftop_min_advance_booking_days', 3) }} days in advance</li>
                        <li>Maximum reservation duration: {{ setting('rooftop_max_duration_hours', 8) }} hours</li>
                        <li>Reservations are subject to approval by management</li>
                    </ul>

                    <p><strong>2. Payment Terms:</strong></p>
                    <ul class="list-disc pl-6 space-y-1">
                        <li>Full payment is required upon booking confirmation</li>
                        <li>Security deposit is refundable subject to property condition</li>
                        <li>Cleaning charges apply for all reservations</li>
                    </ul>

                    <p><strong>3. Usage Rules:</strong></p>
                    <ul class="list-disc pl-6 space-y-1">
                        <li>No smoking or alcohol consumption allowed</li>
                        <li>Music must be kept at reasonable volume levels</li>
                        <li>All decorations must be approved in advance</li>
                        <li>Guest limit strictly enforced</li>
                    </ul>

                    <p><strong>4. Cancellation Policy:</strong></p>
                    <ul class="list-disc pl-6 space-y-1">
                        <li>Cancellations must be made at least {{ setting('rooftop_min_cancellation_hours', 48) }} hours in advance</li>
                        <li>Refunds processed within 5-7 business days</li>
                        <li>Security deposit refunded after property inspection</li>
                    </ul>

                    <p><strong>5. Liability:</strong></p>
                    <ul class="list-disc pl-6 space-y-1">
                        <li>Owner is responsible for any damages during the event</li>
                        <li>Management is not liable for personal belongings</li>
                        <li>Event organizer must ensure guest compliance with building rules</li>
                    </ul>
                </div>
                <div class="mt-6 flex justify-end">
                    <button onclick="acceptTerms()" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Accept & Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Calculate estimated total
        function calculateEstimatedTotal() {
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;
            const guests = document.getElementById('number_of_guests').value;
            
            if (startTime && endTime && guests) {
                const start = new Date('2024-01-01 ' + startTime);
                const end = new Date('2024-01-01 ' + endTime);
                const hours = (end - start) / (1000 * 60 * 60);
                
                if (hours > 0) {
                    const baseRate = {!! json_encode(setting('rooftop_base_rate_per_hour', 500)) !!};
                    const guestCharge = {!! json_encode(setting('rooftop_guest_charge_per_person', 50)) !!};
                    const securityDeposit = {!! json_encode(setting('rooftop_security_deposit', 2000)) !!};
                    const cleaningCharge = {!! json_encode(setting('rooftop_cleaning_charge', 500)) !!};
                    
                    const total = (baseRate * hours) + (guestCharge * guests) + securityDeposit + cleaningCharge;
                    
                    document.getElementById('estimated-total').textContent = 'LKR ' + total.toLocaleString();
                }
            }
        }

        // Event listeners for calculation
        document.getElementById('start_time').addEventListener('change', calculateEstimatedTotal);
        document.getElementById('end_time').addEventListener('change', calculateEstimatedTotal);
        document.getElementById('number_of_guests').addEventListener('input', calculateEstimatedTotal);

        // Terms modal functions
        function showTermsModal() {
            document.getElementById('termsModal').classList.remove('hidden');
        }

        function hideTermsModal() {
            document.getElementById('termsModal').classList.add('hidden');
        }

        function acceptTerms() {
            document.getElementById('terms_accepted').checked = true;
            hideTermsModal();
        }

        // Close modal when clicking outside
        document.getElementById('termsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideTermsModal();
            }
        });
    </script>
    @endpush
</x-app-layout>
