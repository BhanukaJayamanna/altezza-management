<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Rooftop Reservation Details') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Reservation Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    
                    <!-- Breadcrumb -->
                    <x-breadcrumb :items="[
                        ['name' => 'Dashboard', 'url' => route('dashboard')],
                        ['name' => 'Rooftop Reservations', 'url' => route('rooftop-reservations.index')],
                        ['name' => 'Details', 'url' => null]
                    ]" />

                    <!-- Status and Actions Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                            <span class="text-lg font-medium text-gray-900">Reservation #{{ $reservation->id }}</span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                {{ $reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($reservation->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($reservation->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                   'bg-blue-100 text-blue-800')) }}">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            @can('manage_invoices')
                                @if($reservation->status === 'pending')
                                    <form method="POST" action="{{ route('rooftop-reservations.approve', $reservation) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-3 py-1 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                            Approve
                                        </button>
                                    </form>
                                @endif
                                
                                @if(in_array($reservation->status, ['pending', 'approved']))
                                    <a href="{{ route('rooftop-reservations.edit', $reservation) }}" 
                                       class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        Edit
                                    </a>
                                @endif
                                
                                @if(!in_array($reservation->status, ['cancelled', 'completed']))
                                    <form method="POST" action="{{ route('rooftop-reservations.cancel', $reservation) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to cancel this reservation?')"
                                                class="px-3 py-1 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            @endcan

                            <a href="{{ route('rooftop-reservations.pdf', $reservation) }}" target="_blank"
                               class="px-3 py-1 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                Download PDF
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column - Basic Details -->
                        <div class="space-y-6">
                            <!-- Event Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Event Information</h3>
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Event Title</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $reservation->event_title }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Event Type</dt>
                                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $reservation->event_type) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Reservation Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $reservation->reservation_date->format('F j, Y') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Time</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $reservation->start_time->format('h:i A') }} - {{ $reservation->end_time->format('h:i A') }}
                                            <span class="text-gray-500">({{ $reservation->getDurationInHours() }} hours)</span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Number of Guests</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $reservation->number_of_guests }} people</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $reservation->created_at->format('M j, Y \a\t h:i A') }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Owner Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Owner Information</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <span class="text-indigo-600 font-medium text-sm">
                                                    {{ substr($reservation->owner->name, 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $reservation->owner->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $reservation->owner->email }}</p>
                                            <p class="text-sm text-gray-600">{{ $reservation->owner->phone }}</p>
                                            @if($reservation->owner->apartment)
                                                <p class="text-sm text-gray-600">Unit: {{ $reservation->owner->apartment->unit_number }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Special Requirements -->
                            @if($reservation->special_requirements)
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Special Requirements</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-700">{{ $reservation->special_requirements }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Right Column - Financial & Status -->
                        <div class="space-y-6">
                            <!-- Cost Breakdown -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Cost Breakdown</h3>
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <dl class="space-y-3">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Base Amount ({{ $reservation->getDurationInHours() }} hours)</dt>
                                            <dd class="text-sm font-medium text-gray-900">LKR {{ number_format($reservation->base_amount) }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Guest Charges ({{ $reservation->number_of_guests }} guests)</dt>
                                            <dd class="text-sm font-medium text-gray-900">LKR {{ number_format($reservation->guest_charges) }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Security Deposit</dt>
                                            <dd class="text-sm font-medium text-gray-900">LKR {{ number_format($reservation->security_deposit) }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Cleaning Charge</dt>
                                            <dd class="text-sm font-medium text-gray-900">LKR {{ number_format($reservation->cleaning_charge) }}</dd>
                                        </div>
                                        <div class="border-t border-gray-200 pt-3 flex justify-between">
                                            <dt class="text-base font-medium text-gray-900">Total Amount</dt>
                                            <dd class="text-base font-bold text-gray-900">LKR {{ number_format($reservation->total_amount) }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>

                            <!-- Invoice Information -->
                            @if($reservation->invoice)
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Invoice Information</h3>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-sm font-medium text-blue-900">Invoice #INV-{{ $reservation->invoice->id }}</span>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            {{ $reservation->invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                               ($reservation->invoice->status === 'partially_paid' ? 'bg-yellow-100 text-yellow-800' : 
                                               'bg-red-100 text-red-800') }}">
                                            {{ ucfirst(str_replace('_', ' ', $reservation->invoice->status)) }}
                                        </span>
                                    </div>
                                    <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                        <div>
                                            <dt class="text-blue-700">Issue Date</dt>
                                            <dd class="text-blue-900">{{ $reservation->invoice->issue_date->format('M j, Y') }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-blue-700">Due Date</dt>
                                            <dd class="text-blue-900">{{ $reservation->invoice->due_date->format('M j, Y') }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-blue-700">Amount</dt>
                                            <dd class="text-blue-900 font-medium">LKR {{ number_format($reservation->invoice->amount) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-blue-700">Paid</dt>
                                            <dd class="text-blue-900 font-medium">LKR {{ number_format($reservation->invoice->paid_amount) }}</dd>
                                        </div>
                                    </dl>
                                    <div class="mt-3 flex space-x-2">
                                        <a href="{{ route('invoices.show', $reservation->invoice) }}" 
                                           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                            View Invoice
                                        </a>
                                        @if($reservation->invoice->status !== 'paid')
                                        <span class="text-gray-300">|</span>
                                        <a href="{{ route('payments.create', ['invoice_id' => $reservation->invoice->id]) }}" 
                                           class="text-sm text-green-600 hover:text-green-800 font-medium">
                                            Record Payment
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @else
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Invoice Status</h3>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-800">
                                                Invoice will be generated automatically when this reservation is approved.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Activity Timeline -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Activity Timeline</h3>
                                <div class="flow-root">
                                    <ul class="-mb-8">
                                        <li>
                                            <div class="relative pb-8">
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                            <svg class="h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 8.207a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L11.414 9.5z" clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm text-gray-500">
                                                                Reservation created by <span class="font-medium text-gray-900">{{ $reservation->owner->name }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                            {{ $reservation->created_at->format('M j, Y \a\t h:i A') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        @if($reservation->status !== 'pending')
                                        <li>
                                            <div class="relative pb-8">
                                                @if($reservation->status !== 'cancelled')
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full {{ $reservation->status === 'approved' ? 'bg-green-500' : ($reservation->status === 'cancelled' ? 'bg-red-500' : 'bg-gray-500') }} flex items-center justify-center ring-8 ring-white">
                                                            @if($reservation->status === 'approved')
                                                            <svg class="h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                            @elseif($reservation->status === 'cancelled')
                                                            <svg class="h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm text-gray-500">
                                                                Reservation {{ $reservation->status }}
                                                                @if($reservation->invoice && $reservation->status === 'approved')
                                                                <span class="block text-xs text-gray-400">Invoice #INV-{{ $reservation->invoice->id }} generated</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                            {{ $reservation->updated_at->format('M j, Y \a\t h:i A') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @endif

                                        @if($reservation->invoice && $reservation->invoice->payments->count() > 0)
                                        @foreach($reservation->invoice->payments as $payment)
                                        <li>
                                            <div class="relative {{ $loop->last ? '' : 'pb-8' }}">
                                                @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                            <svg class="h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm text-gray-500">
                                                                Payment received: <span class="font-medium text-gray-900">LKR {{ number_format($payment->amount) }}</span>
                                                                <span class="block text-xs text-gray-400">{{ $payment->payment_method }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                            {{ $payment->payment_date->format('M j, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
