<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                    {{ __('Dashboard') }}
                </h2>
                <p class="text-slate-600 text-sm mt-1">Welcome back, {{ Auth::user()->name }}! Here's what's happening with your properties.</p>
            </div>
            <div class="hidden md:flex items-center space-x-3">
                <div class="text-right">
                    <div class="text-sm font-medium text-slate-900">{{ now()->format('l') }}</div>
                    <div class="text-xs text-slate-500">{{ now()->format('F j, Y') }}</div>
                </div>
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @can('view_apartments')
        <x-stat-card
            title="Total Apartments"
            :value="\App\Models\Apartment::count()"
            color="blue"
            :icon="'<svg class=&quot;w-6 h-6 text-white&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4&quot;></path></svg>'"
        />
        @endcan

        @can('view_tenants')
        <x-stat-card
            title="Active Tenants"
            :value="\App\Models\Tenant::where('status', 'active')->count()"
            color="green"
            :icon="'<svg class=&quot;w-6 h-6 text-white&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z&quot;></path></svg>'"
        />
        @endcan

        @can('view_invoices')
        @can('view_invoices')
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        @can('manage_invoices')
                            <div class="text-sm font-medium text-gray-500">Pending Invoices</div>
                            <div class="text-2xl font-bold text-gray-900">{{ \App\Models\Invoice::where('status', 'pending')->count() }}</div>
                        @else
                            @php
                                $userTenant = Auth::user()->tenant;
                                $pendingCount = $userTenant ? \App\Models\Invoice::where('tenant_id', $userTenant->id)->where('status', 'pending')->count() : 0;
                            @endphp
                            <div class="text-sm font-medium text-gray-500">My Pending Invoices</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        @can('manage_invoices')
                            <div class="text-sm font-medium text-gray-500">Monthly Revenue</div>
                            <div class="text-2xl font-bold text-gray-900">@currency(\App\Models\Invoice::where('status', 'paid')->whereMonth('created_at', now()->month)->sum('total_amount'))</div>
                        @else
                            @php
                                $userTenant = Auth::user()->tenant;
                                $monthlyTotal = $userTenant ? \App\Models\Invoice::where('tenant_id', $userTenant->id)->where('status', 'paid')->whereMonth('created_at', now()->month)->sum('total_amount') : 0;
                            @endphp
                            <div class="text-sm font-medium text-gray-500">Monthly Payments</div>
                            <div class="text-2xl font-bold text-gray-900">@currency($monthlyTotal)</div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @endcan
        
        @can('manage_vouchers')
        <!-- Voucher Statistics Cards -->
        <x-stat-card
            title="Pending Vouchers"
            :value="\App\Models\PaymentVoucher::where('status', 'pending')->count()"
            color="amber"
            :icon="'<svg class=&quot;w-6 h-6 text-white&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z&quot;></path></svg>'"
        />

        <x-stat-card
            title="Monthly Expenses"
            :value="'₹ ' . number_format(\App\Models\PaymentVoucher::where('status', 'approved')->whereMonth('created_at', now()->month)->sum('amount'), 2)"
            color="red"
            :icon="'<svg class=&quot;w-6 h-6 text-white&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z&quot;></path></svg>'"
        />
        @endcan
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @can('view_invoices')
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Recent Invoices</h3>
                    @can('manage_invoices')
                        <a href="{{ route('invoices.index') }}" class="text-sm text-cyan-600 hover:text-cyan-500">View all</a>
                    @else
                        <a href="{{ route('tenant.invoices') }}" class="text-sm text-cyan-600 hover:text-cyan-500">View all</a>
                    @endcan
                </div>
                <div class="space-y-3">
                    @can('manage_invoices')
                        @forelse(\App\Models\Invoice::with(['apartment', 'tenant'])->latest()->take(5)->get() as $invoice)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-600">{{ $invoice->apartment->unit_number }}</span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $invoice->tenant->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900">@currency($invoice->total_amount)</span>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">>
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">No invoices found.</p>
                        @endforelse
                    @else
                        @php
                            $userTenant = Auth::user()->tenant;
                            $tenantInvoices = $userTenant ? \App\Models\Invoice::with(['apartment'])->where('tenant_id', $userTenant->id)->latest()->take(5)->get() : collect();
                        @endphp
                        @forelse($tenantInvoices as $invoice)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-600">{{ $invoice->apartment->unit_number }}</span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $invoice->type }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900">@currency($invoice->total_amount)</span>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">>
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">No invoices found.</p>
                        @endforelse
                    @endcan
                </div>
            </div>
        </div>
        @endcan

        @can('manage_vouchers')
        <!-- Recent Payment Vouchers -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Recent Payment Vouchers</h3>
                    <a href="{{ route('vouchers.index') }}" class="text-sm text-cyan-600 hover:text-cyan-500">View all</a>
                </div>
                <div class="space-y-3">
                    @forelse(\App\Models\PaymentVoucher::with(['apartment', 'createdBy'])->latest()->take(5)->get() as $voucher)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $voucher->voucher_number }}</p>
                                <p class="text-sm text-gray-500">{{ $voucher->vendor_name }} - {{ $voucher->apartment ? $voucher->apartment->unit_number : 'General' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-900">₹{{ number_format($voucher->amount, 2) }}</span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $voucher->status === 'approved' ? 'bg-green-100 text-green-800' : ($voucher->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($voucher->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No payment vouchers found.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endcan

        @can('view_apartments')
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Apartment Occupancy</h3>
                    <a href="{{ route('apartments.index') }}" class="text-sm text-cyan-600 hover:text-cyan-500">View all</a>
                </div>
                <div class="space-y-3">
                    @php
                        $occupiedCount = \App\Models\Apartment::where('status', 'occupied')->count();
                        $availableCount = \App\Models\Apartment::where('status', 'available')->count();
                        $maintenanceCount = \App\Models\Apartment::where('status', 'maintenance')->count();
                        $totalCount = \App\Models\Apartment::count();
                        $occupancyRate = $totalCount > 0 ? round(($occupiedCount / $totalCount) * 100) : 0;
                    @endphp
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Occupancy Rate</span>
                        <span class="text-sm font-bold text-gray-900">{{ $occupancyRate }}%</span>
                    </div>
                    
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $occupancyRate }}%"></div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 pt-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $occupiedCount }}</div>
                            <div class="text-xs text-gray-500">Occupied</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $availableCount }}</div>
                            <div class="text-xs text-gray-500">Available</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ $maintenanceCount }}</div>
                            <div class="text-xs text-gray-500">Maintenance</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>
</x-app-layout>
