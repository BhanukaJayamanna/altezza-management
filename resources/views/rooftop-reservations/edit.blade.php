<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Rooftop Reservation') }}
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
                        ['name' => 'Edit', 'url' => null]
                    ]" />

                    <!-- Status Alert -->
                    @if($reservation->status === 'approved')
                        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">
                                        This reservation has been approved. Changes may require re-approval.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('rooftop-reservations.update', $reservation) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

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
                                            <option value="{{ $owner->id }}" {{ (old('owner_id', $reservation->owner_id) == $owner->id) ? 'selected' : '' }}>
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
                                    <input type="text" name="event_title" id="event_title" value="{{ old('event_title', $reservation->event_title) }}" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                           placeholder="Birthday Party, Wedding, etc.">
                                    @error('event_title')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Reservation Date -->
                                <div>
                                    <label for="reservation_date" class="block text-sm font-medium text-gray-700">Reservation Date *</label>
                                    <input type="date" name="reservation_date" id="reservation_date" value="{{ old('reservation_date', $reservation->reservation_date->format('Y-m-d')) }}" required
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
                                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $reservation->start_time->format('H:i')) }}" required
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('start_time')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="end_time" class="block text-sm font-medium text-gray-700">End Time *</label>
                                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $reservation->end_time->format('H:i')) }}" required
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('end_time')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Number of Guests -->
                                <div>
                                    <label for="number_of_guests" class="block text-sm font-medium text-gray-700">Number of Guests *</label>
                                    <input type="number" name="number_of_guests" id="number_of_guests" value="{{ old('number_of_guests', $reservation->number_of_guests) }}" required
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
                                        <option value="birthday" {{ old('event_type', $reservation->event_type) == 'birthday' ? 'selected' : '' }}>Birthday Party</option>
                                        <option value="wedding" {{ old('event_type', $reservation->event_type) == 'wedding' ? 'selected' : '' }}>Wedding</option>
                                        <option value="corporate" {{ old('event_type', $reservation->event_type) == 'corporate' ? 'selected' : '' }}>Corporate Event</option>
                                        <option value="family_gathering" {{ old('event_type', $reservation->event_type) == 'family_gathering' ? 'selected' : '' }}>Family Gathering</option>
                                        <option value="party" {{ old('event_type', $reservation->event_type) == 'party' ? 'selected' : '' }}>General Party</option>
                                        <option value="other" {{ old('event_type', $reservation->event_type) == 'other' ? 'selected' : '' }}>Other</option>
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
                                              placeholder="Any special requirements or notes...">{{ old('special_requirements', $reservation->special_requirements) }}</textarea>
                                    @error('special_requirements')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Current Pricing Information -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Current Charges</h4>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex justify-between">
                                            <span>Base Amount:</span>
                                            <span class="font-medium">LKR {{ number_format($reservation->base_amount) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Guest Charges:</span>
                                            <span class="font-medium">LKR {{ number_format($reservation->guest_charges) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Security Deposit:</span>
                                            <span class="font-medium">LKR {{ number_format($reservation->security_deposit) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Cleaning Charge:</span>
                                            <span class="font-medium">LKR {{ number_format($reservation->cleaning_charge) }}</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="flex justify-between font-semibold">
                                            <span>Current Total:</span>
                                            <span>LKR {{ number_format($reservation->total_amount) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estimated New Pricing -->
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">New Estimate (if changed)</h4>
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
                                            <span>New Total:</span>
                                            <span id="estimated-total">LKR {{ number_format($reservation->total_amount) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Information -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Reservation Status</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Current Status:</span>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                {{ $reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($reservation->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                                   ($reservation->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                                   'bg-blue-100 text-blue-800')) }}">
                                                {{ ucfirst($reservation->status) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Invoice Generated:</span>
                                            <span class="text-sm {{ $reservation->invoice ? 'text-green-600' : 'text-gray-500' }}">
                                                {{ $reservation->invoice ? 'Yes (INV-' . $reservation->invoice->id . ')' : 'No' }}
                                            </span>
                                        </div>
                                        @if($reservation->invoice)
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Payment Status:</span>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                {{ $reservation->invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                                   ($reservation->invoice->status === 'partially_paid' ? 'bg-yellow-100 text-yellow-800' : 
                                                   'bg-red-100 text-red-800') }}">
                                                {{ ucfirst(str_replace('_', ' ', $reservation->invoice->status)) }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('rooftop-reservations.show', $reservation) }}" 
                               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Update Reservation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Store original values for comparison
        const originalStartTime = document.getElementById('start_time').value;
        const originalEndTime = document.getElementById('end_time').value;
        const originalGuests = document.getElementById('number_of_guests').value;
        
        // Calculate estimated total
        function calculateEstimatedTotal() {
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;
            const guests = document.getElementById('number_of_guests').value;
            
            // Check if values have changed
            const hasChanged = startTime !== originalStartTime || 
                              endTime !== originalEndTime || 
                              guests !== originalGuests;
            
            if (startTime && endTime && guests && hasChanged) {
                const start = new Date('2024-01-01 ' + startTime);
                const end = new Date('2024-01-01 ' + endTime);
                const hours = (end - start) / (1000 * 60 * 60);
                
                if (hours > 0) {
                    const baseRate = 500;
                    const guestCharge = 50;
                    const securityDeposit = 2000;
                    const cleaningCharge = 500;
                    
                    const total = (baseRate * hours) + (guestCharge * guests) + securityDeposit + cleaningCharge;
                    
                    document.getElementById('estimated-total').textContent = 'LKR ' + total.toLocaleString();
                }
            } else if (!hasChanged) {
                // Reset to original total if no changes
                document.getElementById('estimated-total').textContent = 'LKR {{ number_format($reservation->total_amount) }}';
            }
        }

        // Event listeners for calculation
        document.getElementById('start_time').addEventListener('change', calculateEstimatedTotal);
        document.getElementById('end_time').addEventListener('change', calculateEstimatedTotal);
        document.getElementById('number_of_guests').addEventListener('input', calculateEstimatedTotal);
    </script>
    @endpush
</x-app-layout>
