<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Utility Reading Details') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('utility-readings.edit', $reading) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Edit Reading
                </a>
                <a href="{{ route('utility-readings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Readings
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            
            <!-- Reading Details -->
            <x-card class="mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Reading Information</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ ucfirst($reading->meter->type) }} reading for {{ $reading->meter->apartment->number }}
                    </p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Reading Details -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Meter</label>
                                <div class="mt-1 text-sm text-gray-900">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $reading->meter->type === 'electricity' ? 'yellow' : ($reading->meter->type === 'water' ? 'blue' : 'orange') }}-100 text-{{ $reading->meter->type === 'electricity' ? 'yellow' : ($reading->meter->type === 'water' ? 'blue' : 'orange') }}-800">
                                            {{ ucfirst($reading->meter->type) }}
                                        </span>
                                        <span>{{ $reading->meter->apartment->number }} - {{ $reading->meter->meter_number }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Reading Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $reading->reading_date->format('M j, Y') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Billing Period</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $reading->billing_period_start->format('M j, Y') }} - {{ $reading->billing_period_end->format('M j, Y') }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Recorded By</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $reading->recordedBy ? $reading->recordedBy->name : 'System' }}
                                </p>
                            </div>
                        </div>

                        <!-- Reading Values -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Previous Reading</label>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($reading->previous_reading, 2) }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Current Reading</label>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($reading->current_reading, 2) }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Consumption</label>
                                <p class="mt-1 text-2xl font-bold text-blue-600">
                                    {{ number_format($reading->consumption, 2) }}
                                    <span class="text-sm font-normal text-gray-500">
                                        {{ $reading->meter->type === 'electricity' ? 'kWh' : ($reading->meter->type === 'water' ? 'gallons' : 'cubic feet') }}
                                    </span>
                                </p>
                            </div>

                            @if($reading->amount)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Calculated Amount</label>
                                <p class="mt-1 text-2xl font-bold text-green-600">@currency($reading->amount)</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($reading->notes)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded-md">
                            <p class="text-sm text-gray-900">{{ $reading->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </x-card>

            <!-- Associated Bills -->
            @if($reading->bill)
            <x-card class="mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Associated Bill</h3>
                </div>

                <div class="p-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">
                                Bill #{{ $reading->bill->bill_number }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                Due: {{ $reading->bill->due_date->format('M j, Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-semibold text-gray-900">
                                @currency($reading->bill->total_amount)
                            </p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $reading->bill->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                   ($reading->bill->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($reading->bill->status) }}
                            </span>
                        </div>
                        <div>
                            <a href="{{ route('utility-bills.show', $reading->bill) }}" 
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                                View Bill
                            </a>
                        </div>
                    </div>
                </div>
            </x-card>
            @endif

            <!-- Actions -->
            <x-card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                </div>

                <div class="p-6">
                    <div class="flex space-x-3">
                        <a href="{{ route('utility-readings.edit', $reading) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Edit Reading
                        </a>

                        @unless($reading->bill)
                        <form method="POST" action="{{ route('utility-readings.destroy', $reading) }}" 
                              class="inline-block"
                              onsubmit="return confirm('Are you sure you want to delete this reading? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                Delete Reading
                            </button>
                        </form>
                        @endunless

                        @if(!$reading->bill)
                        <form method="POST" action="{{ route('utility-readings.generate-bills') }}" class="inline-block">
                            @csrf
                            <input type="hidden" name="reading_ids[]" value="{{ $reading->id }}">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Generate Bill
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </x-card>

        </div>
    </div>
</x-app-layout>
