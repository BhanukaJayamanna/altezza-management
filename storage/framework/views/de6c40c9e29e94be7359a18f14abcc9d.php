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
                <?php echo e(__('Lease Details')); ?> - <?php echo e($lease->lease_number); ?>

            </h2>
            <div class="flex space-x-2">
                <?php if($lease->status === 'active' && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))): ?>
                    <a href="<?php echo e(route('leases.edit', $lease)); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Lease
                    </a>
                    
                    <form action="<?php echo e(route('leases.terminate', $lease)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirm('Are you sure you want to terminate this lease?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Terminate
                        </button>
                    </form>
                <?php endif; ?>

                <a href="<?php echo e(route('leases.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Leases
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
                <!-- Main Lease Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <!-- Lease Header -->
                            <div class="border-b border-gray-200 pb-6 mb-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900"><?php echo e($lease->lease_number); ?></h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                <?php if($lease->status === 'active'): ?> bg-green-100 text-green-800
                                                <?php elseif($lease->status === 'expired'): ?> bg-red-100 text-red-800
                                                <?php elseif($lease->status === 'terminated'): ?> bg-red-100 text-red-800
                                                <?php else: ?> bg-yellow-100 text-yellow-800
                                                <?php endif; ?>">
                                                <?php echo e(ucfirst($lease->status)); ?>

                                            </span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-gray-900">
                                            <?php echo currency($lease->rent_amount); ?>/month
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Created: <?php echo e($lease->created_at->format('M d, Y')); ?>

                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Lease Period -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Start Date</h4>
                                    <p class="text-lg font-semibold text-gray-700"><?php echo e($lease->start_date->format('M d, Y')); ?></p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">End Date</h4>
                                    <p class="text-lg font-semibold text-gray-700"><?php echo e($lease->end_date->format('M d, Y')); ?></p>
                                </div>
                            </div>

                            <!-- Financial Details -->
                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-3">Financial Details</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-gray-700">Monthly Rent</h5>
                                        <p class="text-xl font-bold text-blue-600"><?php echo currency($lease->rent_amount); ?></p>
                                    </div>
                                    <div class="bg-green-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-gray-700">Security Deposit</h5>
                                        <p class="text-xl font-bold text-green-600"><?php echo currency($lease->security_deposit); ?></p>
                                    </div>
                                    <div class="bg-yellow-50 p-4 rounded-lg">
                                        <h5 class="text-sm font-medium text-gray-700">Maintenance Charge</h5>
                                        <p class="text-xl font-bold text-yellow-600"><?php echo currency($lease->maintenance_charge); ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <?php if($lease->terms_conditions && count($lease->terms_conditions) > 0): ?>
                                <div class="mb-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-3">Terms & Conditions</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <ul class="space-y-2">
                                            <?php $__currentLoopData = $lease->terms_conditions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="flex items-start">
                                                    <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-sm text-gray-700"><?php echo e($term); ?></span>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Contract File -->
                    <?php if($lease->contract_file): ?>
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Contract Document</h3>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Contract.pdf</p>
                                            <p class="text-xs text-gray-500">Click to download</p>
                                        </div>
                                    </div>
                                    <a href="<?php echo e(asset('storage/' . $lease->contract_file)); ?>" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Tenant Information -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Tenant Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Name</p>
                                    <p class="text-sm text-gray-900"><?php echo e($lease->tenant->name); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <p class="text-sm text-gray-900"><?php echo e($lease->tenant->email); ?></p>
                                </div>
                                <?php if($lease->tenant->phone): ?>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Phone</p>
                                        <p class="text-sm text-gray-900"><?php echo e($lease->tenant->phone); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Apartment Information -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Apartment Details</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Unit Number</p>
                                    <p class="text-sm text-gray-900"><?php echo e($lease->apartment->number); ?></p>
                                </div>
                                <?php if($lease->apartment->block): ?>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Block</p>
                                        <p class="text-sm text-gray-900"><?php echo e($lease->apartment->block); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if($lease->apartment->floor): ?>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Floor</p>
                                        <p class="text-sm text-gray-900"><?php echo e($lease->apartment->floor); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if($lease->apartment->type): ?>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Type</p>
                                        <p class="text-sm text-gray-900"><?php echo e(ucfirst($lease->apartment->type)); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Owner Information -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Owner Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Name</p>
                                    <p class="text-sm text-gray-900"><?php echo e($lease->owner->name); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <p class="text-sm text-gray-900"><?php echo e($lease->owner->email); ?></p>
                                </div>
                                <?php if($lease->owner->phone): ?>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Phone</p>
                                        <p class="text-sm text-gray-900"><?php echo e($lease->owner->phone); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
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
<?php /**PATH F:\APPS\altezza\altezza\altezza\resources\views/leases/show.blade.php ENDPATH**/ ?>