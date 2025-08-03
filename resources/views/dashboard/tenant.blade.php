<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tenant Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Welcome back, {{ Auth::user()->name }}!</h3>
                            @if($stats['current_apartment'])
                                <p class="text-gray-600">Apartment {{ $stats['current_apartment']->number }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</p>
                            <p class="text-sm text-gray-500">{{ now()->format('g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Pending Invoices -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Pending Invoices</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_invoices'] }}</p>
                            </div>
                        </div>
                        @if($stats['pending_invoices'] > 0)
                            <div class="mt-3">
                                <a href="{{ route('tenant.invoices', ['status' => 'pending']) }}" class="text-sm text-yellow-600 hover:text-yellow-700">
                                    View pending invoices →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Overdue Invoices -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Overdue Invoices</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['overdue_invoices'] }}</p>
                            </div>
                        </div>
                        @if($stats['overdue_invoices'] > 0)
                            <div class="mt-3">
                                <a href="{{ route('tenant.invoices', ['status' => 'overdue']) }}" class="text-sm text-red-600 hover:text-red-700">
                                    Pay overdue invoices →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Maintenance Requests -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Active Maintenance</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['maintenance_requests'] }}</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('tenant.maintenance-requests') }}" class="text-sm text-blue-600 hover:text-blue-700">
                                View requests →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Open Complaints -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Open Complaints</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['open_complaints'] }}</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('tenant.complaints') }}" class="text-sm text-orange-600 hover:text-orange-700">
                                View complaints →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Invoices -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Recent Invoices</h3>
                            <a href="{{ route('tenant.invoices') }}" class="text-sm text-blue-600 hover:text-blue-700">
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @forelse($recentActivities['recent_invoices'] as $invoice)
                            <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        Invoice #{{ $invoice->invoice_number }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $invoice->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                        ($invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-900">
                                        ${{ number_format($invoice->amount, 2) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No recent invoices</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Maintenance Requests -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Recent Maintenance</h3>
                            <a href="{{ route('tenant.maintenance-requests') }}" class="text-sm text-blue-600 hover:text-blue-700">
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @forelse($recentActivities['recent_maintenance'] as $request)
                            <div class="flex items-start justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $request->title }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $request->created_at->format('M d, Y') }} • 
                                        Priority: {{ ucfirst($request->priority) }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $request->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($request->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ str_replace('_', ' ', ucfirst($request->status)) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No recent maintenance requests</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('tenant.maintenance-requests.create') }}" 
                           class="flex flex-col items-center p-4 border-2 border-gray-200 border-dashed rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
                            <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Request Maintenance</span>
                        </a>

                        <a href="{{ route('tenant.complaints.create') }}" 
                           class="flex flex-col items-center p-4 border-2 border-gray-200 border-dashed rounded-lg hover:border-orange-300 hover:bg-orange-50 transition-colors">
                            <svg class="w-8 h-8 text-orange-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">File Complaint</span>
                        </a>

                        <a href="{{ route('tenant.invoices') }}" 
                           class="flex flex-col items-center p-4 border-2 border-gray-200 border-dashed rounded-lg hover:border-green-300 hover:bg-green-50 transition-colors">
                            <svg class="w-8 h-8 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">View Invoices</span>
                        </a>

                        <a href="{{ route('tenant.notices') }}" 
                           class="flex flex-col items-center p-4 border-2 border-gray-200 border-dashed rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors">
                            <svg class="w-8 h-8 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM8.515 2l5.801 5.998h5.684v5.002h-5.684L8.515 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">View Notices</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
