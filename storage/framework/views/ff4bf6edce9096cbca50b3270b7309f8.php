<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e(__('Payment Details')); ?> - #<?php echo e($payment->id); ?>

            </h2>
            <div class="flex space-x-2">
                <?php if($payment->status === 'pending' && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))): ?>
                    <form action="<?php echo e(route('payments.approve', $payment)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700" onclick="return confirm('Are you sure you want to approve this payment?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Approve Payment
                        </button>
                    </form>
                <?php endif; ?>

                <?php if($payment->status !== 'cancelled' && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))): ?>
                    <a href="<?php echo e(route('payments.edit', $payment)); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Payment
                    </a>
                <?php endif; ?>

                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>

                <a href="<?php echo e(route('payments.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Payments
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Payment Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <!-- Payment Header -->
                            <div class="border-b border-gray-200 pb-6 mb-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">Payment #<?php echo e($payment->id); ?></h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Amount: <span class="font-semibold text-lg">$<?php echo e(number_format($payment->amount, 2)); ?></span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <?php
                                            $statusLabel = $payment->status_label;
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php if($statusLabel['color'] === 'green'): ?> bg-green-100 text-green-800
                                            <?php elseif($statusLabel['color'] === 'yellow'): ?> bg-yellow-100 text-yellow-800
                                            <?php elseif($statusLabel['color'] === 'red'): ?> bg-red-100 text-red-800
                                            <?php else: ?> bg-gray-100 text-gray-800
                                            <?php endif; ?>">
                                            <?php echo e($statusLabel['label']); ?>

                                        </span>
                                        <p class="text-sm text-gray-600 mt-2">
                                            Payment Date: <?php echo e($payment->payment_date->format('M d, Y')); ?>

                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Recorded: <?php echo e($payment->created_at->format('M d, Y H:i')); ?>

                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Payment Details</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Method</label>
                                            <p class="text-sm text-gray-900"><?php echo e($payment->method_label); ?></p>
                                        </div>
                                        <?php if($payment->reference_number): ?>
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Reference Number</label>
                                                <p class="text-sm text-gray-900"><?php echo e($payment->reference_number); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($payment->notes): ?>
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Notes</label>
                                                <p class="text-sm text-gray-900"><?php echo e($payment->notes); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Tenant Information</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="font-medium text-gray-900"><?php echo e($payment->tenant->name); ?></p>
                                        <p class="text-sm text-gray-600"><?php echo e($payment->tenant->email); ?></p>
                                        <?php if($payment->tenant->phone): ?>
                                            <p class="text-sm text-gray-600"><?php echo e($payment->tenant->phone); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Information -->
                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-3">Related Invoice</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900"><?php echo e($payment->invoice->invoice_number); ?></p>
                                            <p class="text-sm text-gray-600">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    <?php if($payment->invoice->type === 'rent'): ?> bg-blue-100 text-blue-800
                                                    <?php elseif($payment->invoice->type === 'utility'): ?> bg-green-100 text-green-800
                                                    <?php elseif($payment->invoice->type === 'maintenance'): ?> bg-yellow-100 text-yellow-800
                                                    <?php else: ?> bg-gray-100 text-gray-800
                                                    <?php endif; ?>">
                                                    <?php echo e(ucfirst($payment->invoice->type)); ?>

                                                </span>
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                Apartment <?php echo e($payment->invoice->apartment->number); ?>

                                                <?php if($payment->invoice->apartment->block): ?>
                                                    - Block <?php echo e($payment->invoice->apartment->block); ?>

                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">
                                                Total: $<?php echo e(number_format($payment->invoice->total_amount, 2)); ?>

                                            </p>
                                            <a href="<?php echo e(route('invoices.show', $payment->invoice)); ?>" class="text-sm text-indigo-600 hover:text-indigo-900 mt-1 inline-block">
                                                View Invoice →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if($payment->receipt_file): ?>
                                <!-- Receipt File -->
                                <div class="mb-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Receipt</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <a href="<?php echo e(asset('storage/' . $payment->receipt_file)); ?>" target="_blank" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download Receipt
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Summary</h3>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Payment Amount</span>
                                    <span class="text-sm font-medium text-gray-900">$<?php echo e(number_format($payment->amount, 2)); ?></span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Payment Method</span>
                                    <span class="text-sm font-medium text-gray-900"><?php echo e($payment->method_label); ?></span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status</span>
                                    <span class="text-sm font-medium text-gray-900"><?php echo e($payment->status_label['label']); ?></span>
                                </div>
                                
                                <div class="flex justify-between pt-4 border-t border-gray-200">
                                    <span class="text-sm text-gray-600">Invoice Total</span>
                                    <span class="text-sm font-medium text-gray-900">$<?php echo e(number_format($payment->invoice->total_amount, 2)); ?></span>
                                </div>
                                
                                <?php
                                    $totalPaid = $payment->invoice->payments()->where('status', 'completed')->sum('amount');
                                    $remainingBalance = $payment->invoice->total_amount - $totalPaid;
                                ?>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Total Paid</span>
                                    <span class="text-sm font-medium text-gray-900">$<?php echo e(number_format($totalPaid, 2)); ?></span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Remaining Balance</span>
                                    <span class="text-sm font-medium <?php echo e($remainingBalance > 0 ? 'text-red-600' : 'text-green-600'); ?>">
                                        $<?php echo e(number_format($remainingBalance, 2)); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recorded By -->
                    <?php if($payment->recordedBy): ?>
                        <div class="bg-white overflow-hidden shadow rounded-lg mt-6">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Recorded By</h3>
                                
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                <?php echo e(substr($payment->recordedBy->name, 0, 1)); ?>

                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900"><?php echo e($payment->recordedBy->name); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo e(ucfirst($payment->recordedBy->role)); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo e($payment->created_at->format('M d, Y H:i')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            .print-only {
                display: block !important;
            }
            
            body {
                font-size: 12px;
            }
            
            .bg-gray-50 {
                background-color: #f9f9f9 !important;
            }
        }
    </style>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH F:\APPS\altezza\altezza\altezza\resources\views/payments/show.blade.php ENDPATH**/ ?>