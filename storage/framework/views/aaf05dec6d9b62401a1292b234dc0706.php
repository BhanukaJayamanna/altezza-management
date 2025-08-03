<!-- Sidebar content -->
<div class="flex flex-col flex-grow bg-white overflow-y-auto">
    <!-- Logo -->
    <div class="flex items-center flex-shrink-0 px-6 py-6 border-b border-slate-100">
        <div class="flex items-center">
            <div class="h-10 w-10 bg-gradient-to-br from-blue-600 to-purple-700 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Altezza</h1>
                <p class="text-xs text-slate-500 font-medium">Property Management</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <!-- Dashboard -->
        <a href="<?php echo e(route('dashboard')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-50 to-purple-50 text-blue-700 shadow-sm border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
            <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v4H8V5z"></path>
                </svg>
            </div>
            Dashboard
        </a>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_apartments')): ?>
        <!-- Master Data Section -->
        <div class="pt-6">
            <div class="px-4 mb-4">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Master Data</h3>
            </div>
            
            <!-- Apartments -->
            <a href="<?php echo e(route('apartments.index')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('apartments.*') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 shadow-sm border border-emerald-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('apartments.*') ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                Apartments
            </a>

            <!-- Tenants -->
            <a href="<?php echo e(route('tenants.index')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('tenants.*') ? 'bg-gradient-to-r from-amber-50 to-orange-50 text-amber-700 shadow-sm border border-amber-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('tenants.*') ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                Tenants
            </a>

            <!-- Owners -->
            <a href="<?php echo e(route('owners.index')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('owners.*') ? 'bg-gradient-to-r from-indigo-50 to-blue-50 text-indigo-700 shadow-sm border border-indigo-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('owners.*') ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                Owners
            </a>

            <!-- Leases -->
            <a href="<?php echo e(route('leases.index')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('leases.*') ? 'bg-gradient-to-r from-violet-50 to-purple-50 text-violet-700 shadow-sm border border-violet-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('leases.*') ? 'bg-violet-100 text-violet-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                Leases
            </a>
        </div>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_invoices')): ?>
        <!-- Financial Management Section -->
        <div class="pt-6">
            <div class="px-4 mb-4">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Financial Management</h3>
            </div>
            
            <!-- Invoices -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_invoices')): ?>
                <a href="<?php echo e(route('invoices.index')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('invoices.*') ? 'bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 shadow-sm border border-green-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                    <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('invoices.*') ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                        </svg>
                    </div>
                    Invoices & Billing
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('tenant.invoices')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('tenant.invoices*') ? 'bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 shadow-sm border border-green-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                    <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('tenant.invoices*') ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                        </svg>
                    </div>
                    My Invoices
                </a>
            <?php endif; ?>

            <!-- Payments -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_payments')): ?>
                <a href="<?php echo e(route('payments.index')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('payments.*') ? 'bg-gradient-to-r from-blue-50 to-cyan-50 text-blue-700 shadow-sm border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                    <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('payments.*') ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    Payment Management
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('tenant.payments')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('tenant.payments*') ? 'bg-gradient-to-r from-blue-50 to-cyan-50 text-blue-700 shadow-sm border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                    <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('tenant.payments*') ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    My Payments
                </a>
            <?php endif; ?>

            <!-- Payment Vouchers -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_vouchers')): ?>
                <a href="<?php echo e(route('vouchers.index')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('vouchers.*') ? 'bg-gradient-to-r from-emerald-50 to-green-50 text-emerald-700 shadow-sm border border-emerald-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                    <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('vouchers.*') ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    Payment Vouchers
                </a>
            <?php endif; ?>

            <!-- Utility Management Section -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_utilities')): ?>
                <!-- Utility Bills (Admin/Manager) -->
                <a href="<?php echo e(route('utility-bills.index')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('utility-bills.*') ? 'bg-gradient-to-r from-orange-50 to-amber-50 text-orange-700 shadow-sm border border-orange-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                    <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('utility-bills.*') ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    Utility Bills
                </a>

                <!-- Utility Meters -->
                <a href="<?php echo e(route('utility-meters.index')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('utility-meters.*') ? 'bg-gradient-to-r from-teal-50 to-cyan-50 text-teal-700 shadow-sm border border-teal-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                    <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('utility-meters.*') ? 'bg-teal-100 text-teal-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    Utility Meters
                </a>

                <!-- Utility Readings -->
                <a href="<?php echo e(route('utility-readings.index')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('utility-readings.*') ? 'bg-gradient-to-r from-indigo-50 to-blue-50 text-indigo-700 shadow-sm border border-indigo-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                    <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('utility-readings.*') ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    Meter Readings
                </a>

                <!-- Unit Prices -->
                <a href="<?php echo e(route('utility-unit-prices.index')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('utility-unit-prices.*') ? 'bg-gradient-to-r from-pink-50 to-rose-50 text-pink-700 shadow-sm border border-pink-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                    <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('utility-unit-prices.*') ? 'bg-pink-100 text-pink-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    Unit Prices
                </a>
            <?php else: ?>
                <!-- Tenant Utility Bills -->
                <a href="<?php echo e(route('tenant.utility-bills')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('tenant.utility-bills*') ? 'bg-gradient-to-r from-orange-50 to-amber-50 text-orange-700 shadow-sm border border-orange-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                    <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('tenant.utility-bills*') ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    My Utility Bills
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_settings')): ?>
        <!-- Administration Section (Admin Only) -->
        <div class="pt-6">
            <div class="px-4 mb-4">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Administration</h3>
            </div>
            
            <!-- System Settings -->
            <a href="<?php echo e(route('settings.index')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('settings.*') ? 'bg-gradient-to-r from-purple-50 to-indigo-50 text-purple-700 shadow-sm border border-purple-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('settings.*') ? 'bg-purple-100 text-purple-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                System Settings
            </a>
            
            <!-- Toast Demo (Development) -->
            <a href="<?php echo e(route('toast.demo')); ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo e(request()->routeIs('toast.*') ? 'bg-gradient-to-r from-orange-50 to-red-50 text-orange-700 shadow-sm border border-orange-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
                <div class="mr-3 h-8 w-8 <?php echo e(request()->routeIs('toast.*') ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200 group-hover:text-slate-600'); ?> rounded-lg flex items-center justify-center transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7 7 0 10-14 0v5h5l-5 5-5-5h5V7a9 9 0 1118 0v10z"></path>
                    </svg>
                </div>
                Toast Demo
                <span class="ml-auto px-2 py-1 text-xs bg-orange-100 text-orange-600 rounded-full">DEV</span>
            </a>
        </div>
        <?php endif; ?>

    </nav>

    <!-- User info at bottom -->
    <div class="flex-shrink-0 flex border-t border-slate-100 p-6 bg-slate-50/50">
        <div class="flex-shrink-0 w-full group block">
            <div class="flex items-center">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold shadow-lg">
                    <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-semibold text-slate-700 group-hover:text-slate-900"><?php echo e(Auth::user()->name); ?></p>
                    <p class="text-xs font-medium text-slate-500 group-hover:text-slate-700 capitalize"><?php echo e(Auth::user()->role); ?></p>
                </div>
                <div class="ml-2">
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH F:\APPS\altezza\altezza\altezza\resources\views/layouts/sidebar-content.blade.php ENDPATH**/ ?>