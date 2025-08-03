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
                <?php echo e(__('Invoice Details')); ?> - <?php echo e($invoice->invoice_number); ?>

            </h2>
            <div class="flex space-x-2">
                <?php if($invoice->status === 'pending' && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))): ?>
                    <a href="<?php echo e(route('invoices.edit', $invoice)); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Invoice
                    </a>
                <?php endif; ?>
                
                <?php if($invoice->status === 'pending' && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))): ?>
                    <form action="<?php echo e(route('invoices.mark-paid', $invoice)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700" onclick="return confirm('Are you sure you want to mark this invoice as paid?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark as Paid
                        </button>
                    </form>
                <?php endif; ?>

                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>

                <a href="<?php echo e(route('invoices.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Invoices
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            <?php if(session('success')): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Invoice Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <!-- Invoice Header -->
                            <div class="border-b border-gray-200 pb-6 mb-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900"><?php echo e($invoice->invoice_number); ?></h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                <?php if($invoice->type === 'rent'): ?> bg-blue-100 text-blue-800
                                                <?php elseif($invoice->type === 'utility'): ?> bg-green-100 text-green-800
                                                <?php elseif($invoice->type === 'maintenance'): ?> bg-yellow-100 text-yellow-800
                                                <?php else: ?> bg-gray-100 text-gray-800
                                                <?php endif; ?>">
                                                <?php echo e(ucfirst($invoice->type)); ?>

                                            </span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php if($invoice->status === 'paid'): ?> bg-green-100 text-green-800
                                            <?php elseif($invoice->status === 'pending'): ?> bg-yellow-100 text-yellow-800
                                            <?php elseif($invoice->status === 'overdue'): ?> bg-red-100 text-red-800
                                            <?php else: ?> bg-gray-100 text-gray-800
                                            <?php endif; ?>">
                                            <?php echo e(ucfirst($invoice->status)); ?>

                                        </span>
                                        <p class="text-sm text-gray-600 mt-2">
                                            Issue Date: <?php echo e($invoice->created_at->format('M d, Y')); ?>

                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Due Date: <?php echo e(\Carbon\Carbon::parse($invoice->due_date)->format('M d, Y')); ?>

                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Bill To</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="font-medium text-gray-900"><?php echo e($invoice->tenant->name); ?></p>
                                        <p class="text-sm text-gray-600"><?php echo e($invoice->tenant->email); ?></p>
                                        <?php if($invoice->tenant->phone): ?>
                                            <p class="text-sm text-gray-600"><?php echo e($invoice->tenant->phone); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Property Details</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="font-medium text-gray-900">Apartment <?php echo e($invoice->apartment->number); ?></p>
                                        <?php if($invoice->apartment->block): ?>
                                            <p class="text-sm text-gray-600">Block <?php echo e($invoice->apartment->block); ?></p>
                                        <?php endif; ?>
                                        <?php if($invoice->apartment->floor): ?>
                                            <p class="text-sm text-gray-600">Floor <?php echo e($invoice->apartment->floor); ?></p>
                                        <?php endif; ?>
                                        <?php if($invoice->apartment->type): ?>
                                            <p class="text-sm text-gray-600"><?php echo e(ucfirst($invoice->apartment->type)); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Period -->
                            <?php if($invoice->billing_period_start && $invoice->billing_period_end): ?>
                                <div class="mb-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Billing Period</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm text-gray-600">
                                            <?php echo e(\Carbon\Carbon::parse($invoice->billing_period_start)->format('M d, Y')); ?> - 
                                            <?php echo e(\Carbon\Carbon::parse($invoice->billing_period_end)->format('M d, Y')); ?>

                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Line Items -->
                            <?php if($invoice->line_items): ?>
                                <?php
                                    $lineItems = is_string($invoice->line_items) ? json_decode($invoice->line_items, true) : $invoice->line_items;
                                ?>
                                <?php if($lineItems && count($lineItems) > 0): ?>
                                    <div class="mb-6">
                                        <h4 class="text-lg font-medium text-gray-900 mb-3">Item Details</h4>
                                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                            <table class="min-w-full divide-y divide-gray-300">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    <?php $__currentLoopData = $lineItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                <?php echo e($item['description'] ?? 'N/A'); ?>

                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                <?php echo e($item['quantity'] ?? 1); ?>

                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                $<?php echo e(number_format($item['rate'] ?? 0, 2)); ?>

                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                $<?php echo e(number_format($item['amount'] ?? 0, 2)); ?>

                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Description -->
                            <?php if($invoice->description): ?>
                                <div class="mb-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Description</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm text-gray-600"><?php echo e($invoice->description); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Amount Breakdown -->
                            <div class="border-t border-gray-200 pt-6">
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>Subtotal:</span>
                                        <span>$<?php echo e(number_format($invoice->amount, 2)); ?></span>
                                    </div>
                                    
                                    <?php if($invoice->late_fee > 0): ?>
                                        <div class="flex justify-between text-sm text-gray-600">
                                            <span>Late Fee:</span>
                                            <span>$<?php echo e(number_format($invoice->late_fee, 2)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if($invoice->discount > 0): ?>
                                        <div class="flex justify-between text-sm text-green-600">
                                            <span>Discount:</span>
                                            <span>-$<?php echo e(number_format($invoice->discount, 2)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex justify-between text-lg font-bold text-gray-900 border-t border-gray-200 pt-3">
                                        <span>Total Amount:</span>
                                        <span>$<?php echo e(number_format($invoice->total_amount, 2)); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Payment Information -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                            
                            <?php if($invoice->status === 'paid'): ?>
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Paid On:</span>
                                        <span class="text-gray-900"><?php echo e(\Carbon\Carbon::parse($invoice->paid_on)->format('M d, Y')); ?></span>
                                    </div>
                                    <?php if($invoice->payment_method): ?>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Payment Method:</span>
                                            <span class="text-gray-900"><?php echo e(ucfirst($invoice->payment_method)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <p class="text-sm text-gray-600">
                                        <?php if($invoice->status === 'pending'): ?>
                                            Payment pending
                                        <?php elseif($invoice->status === 'overdue'): ?>
                                            Payment overdue
                                        <?php endif; ?>
                                    </p>
                                    <?php if($invoice->due_date && \Carbon\Carbon::parse($invoice->due_date)->isPast()): ?>
                                        <p class="text-xs text-red-600 mt-1">
                                            Due <?php echo e(\Carbon\Carbon::parse($invoice->due_date)->diffForHumans()); ?>

                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Payment History -->
                    <?php if($invoice->payments && $invoice->payments->count() > 0): ?>
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment History</h3>
                                <div class="space-y-3">
                                    <?php $__currentLoopData = $invoice->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">$<?php echo e(number_format($payment->amount, 2)); ?></p>
                                                <p class="text-xs text-gray-600"><?php echo e($payment->created_at->format('M d, Y')); ?></p>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                <?php if($payment->status === 'completed'): ?> bg-green-100 text-green-800
                                                <?php elseif($payment->status === 'pending'): ?> bg-yellow-100 text-yellow-800
                                                <?php else: ?> bg-red-100 text-red-800
                                                <?php endif; ?>">
                                                <?php echo e(ucfirst($payment->status)); ?>

                                            </span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Created By -->
                    <?php if($invoice->createdBy): ?>
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Created By</h3>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                <?php echo e(substr($invoice->createdBy->name, 0, 1)); ?>

                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900"><?php echo e($invoice->createdBy->name); ?></p>
                                        <p class="text-xs text-gray-600"><?php echo e($invoice->created_at->format('M d, Y g:i A')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
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
<?php /**PATH F:\APPS\altezza\altezza\altezza\resources\views/invoices/show.blade.php ENDPATH**/ ?>