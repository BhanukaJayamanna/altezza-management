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
                <?php echo e(__('Utility Meter Details')); ?>

            </h2>
            <div class="flex space-x-2">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
                <a href="<?php echo e(route('utility-meters.edit', $meter)); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Edit Meter
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('utility-meters.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Meters
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Meter Information Card -->
            <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Meter Information</h3>
                        <span class="px-3 py-1 text-sm rounded-full 
                            <?php echo e($meter->status === 'active' ? 'bg-green-100 text-green-800' : 
                               ($meter->status === 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800')); ?>">
                            <?php echo e(ucfirst($meter->status)); ?>

                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Apartment</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="<?php echo e(route('apartments.show', $meter->apartment)); ?>" class="text-blue-600 hover:text-blue-800">
                                    <?php echo e($meter->apartment->number); ?> - <?php echo e($meter->apartment->type); ?>

                                </a>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Block</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo e($meter->apartment->block); ?></dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Utility Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    <?php echo e($meter->type === 'electricity' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($meter->type === 'water' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800')); ?>">
                                    <?php echo e(ucfirst($meter->type)); ?>

                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Meter Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono"><?php echo e($meter->meter_number); ?></dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Rate Per Unit</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo currency($meter->rate_per_unit, 4); ?></dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Current Reading</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <?php echo e($meter->last_reading ? number_format($meter->last_reading, 2) : 'No reading yet'); ?>

                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Reading Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <?php echo e($meter->last_reading_date ? $meter->last_reading_date->format('M j, Y') : 'No date recorded'); ?>

                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo e($meter->created_at->format('M j, Y g:i A')); ?></dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo e($meter->updated_at->format('M j, Y g:i A')); ?></dd>
                        </div>
                    </dl>

                    <?php if($meter->notes): ?>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Notes</dt>
                        <dd class="text-sm text-gray-900 bg-gray-50 p-4 rounded-md"><?php echo e($meter->notes); ?></dd>
                    </div>
                    <?php endif; ?>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>

            <!-- Recent Readings -->
            <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Recent Readings</h3>
                        <a href="<?php echo e(route('utility-readings.create', ['meter_id' => $meter->id])); ?>" 
                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Add Reading
                        </a>
                    </div>
                </div>

                <div class="overflow-hidden">
                    <?php if($meter->readings()->count() > 0): ?>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reading</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recorded By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $meter->readings()->latest()->take(10)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reading): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($reading->reading_date->format('M j, Y')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                    <?php echo e(number_format($reading->current_reading, 2)); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($reading->consumption ? number_format($reading->consumption, 2) : '-'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo currency($reading->amount); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($reading->recordedBy->name); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
                                        <a href="<?php echo e(route('utility-readings.edit', $reading)); ?>" class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <form method="POST" action="<?php echo e(route('utility-readings.destroy', $reading)); ?>" 
                                              onsubmit="return confirm('Are you sure you want to delete this reading?')" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                    <?php if($meter->readings()->count() > 10): ?>
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                        <a href="<?php echo e(route('utility-readings.index', ['meter_id' => $meter->id])); ?>" 
                           class="text-sm text-blue-600 hover:text-blue-800">
                            View all <?php echo e($meter->readings()->count()); ?> readings →
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php else: ?>
                    <div class="px-6 py-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No readings yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding the first reading for this meter.</p>
                        <div class="mt-6">
                            <a href="<?php echo e(route('utility-readings.create', ['meter_id' => $meter->id])); ?>" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Add First Reading
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>

            <!-- Current Tenant Information -->
            <?php if($meter->apartment->currentLease): ?>
            <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Current Tenant</h3>
                </div>

                <div class="p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">
                                <a href="<?php echo e(route('tenants.show', $meter->apartment->currentLease->tenant)); ?>" 
                                   class="text-blue-600 hover:text-blue-800">
                                    <?php echo e($meter->apartment->currentLease->tenant->name); ?>

                                </a>
                            </h4>
                            <p class="text-sm text-gray-500"><?php echo e($meter->apartment->currentLease->tenant->email); ?></p>
                            <p class="text-sm text-gray-500">
                                Lease: <?php echo e($meter->apartment->currentLease->start_date->format('M j, Y')); ?> - 
                                <?php echo e($meter->apartment->currentLease->end_date->format('M j, Y')); ?>

                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Monthly Rent</p>
                            <p class="text-lg font-medium text-gray-900"><?php echo currency($meter->apartment->currentLease->monthly_rent); ?></p>
                        </div>
                    </div>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
            <?php endif; ?>

            <!-- Usage Statistics -->
            <?php if($meter->readings()->count() > 1): ?>
            <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Usage Statistics</h3>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div class="bg-gray-50 px-4 py-5 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 truncate">Last Month Usage</dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                <?php echo e(number_format($meter->readings()->whereMonth('reading_date', now()->subMonth()->month)->sum('consumption'), 2)); ?>

                            </dd>
                        </div>

                        <div class="bg-blue-50 px-4 py-5 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 truncate">This Month Usage</dt>
                            <dd class="mt-1 text-3xl font-semibold text-blue-600">
                                <?php echo e(number_format($meter->readings()->whereMonth('reading_date', now()->month)->sum('consumption'), 2)); ?>

                            </dd>
                        </div>

                        <div class="bg-green-50 px-4 py-5 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 truncate">Average Monthly</dt>
                            <dd class="mt-1 text-3xl font-semibold text-green-600">
                                <?php echo e(number_format($meter->readings()->avg('consumption'), 2)); ?>

                            </dd>
                        </div>
                    </dl>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
            <?php endif; ?>
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
<?php /**PATH F:\APPS\altezza\altezza\altezza\resources\views/utilities/meters/show.blade.php ENDPATH**/ ?>