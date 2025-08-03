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
                <?php echo e(__('Unit Price Details')); ?>

            </h2>
            <div class="flex space-x-2">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin')): ?>
                <?php if($unitPrice->status === 'active'): ?>
                <a href="<?php echo e(route('utility-unit-prices.edit', $unitPrice)); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Edit Price
                </a>
                <?php endif; ?>
                <?php endif; ?>
                <a href="<?php echo e(route('utility-unit-prices.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Unit Prices
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Unit Price Information Card -->
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
                        <h3 class="text-lg font-medium text-gray-900">Unit Price Information</h3>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                <?php echo e($unitPrice->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                                <?php echo e(ucfirst($unitPrice->status)); ?>

                            </span>
                            <?php if($unitPrice->isCurrent()): ?>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                Current Price
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Utility Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    <?php echo e($unitPrice->type === 'electricity' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($unitPrice->type === 'water' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800')); ?>">
                                    <?php echo e(ucfirst($unitPrice->type)); ?>

                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Price Per Unit</dt>
                            <dd class="mt-1 text-2xl font-bold text-gray-900">
                                <?php echo currency($unitPrice->price_per_unit, 4); ?>
                                <span class="text-sm font-normal text-gray-500">
                                    per <?php echo e($unitPrice->type === 'electricity' ? 'kWh' : ($unitPrice->type === 'water' ? 'gallon' : 'cubic foot')); ?>

                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Effective Period</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <div>From: <?php echo e($unitPrice->effective_from ? $unitPrice->effective_from->format('M j, Y') : 'Not set'); ?></div>
                                <div>Until: <?php echo e($unitPrice->effective_to ? $unitPrice->effective_to->format('M j, Y') : 'Ongoing'); ?></div>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    <?php echo e($unitPrice->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo e(ucfirst($unitPrice->status)); ?>

                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo e($unitPrice->created_at ? $unitPrice->created_at->format('M j, Y g:i A') : 'Not available'); ?></dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo e($unitPrice->updated_at ? $unitPrice->updated_at->format('M j, Y g:i A') : 'Not available'); ?></dd>
                        </div>
                    </dl>

                    <?php if($unitPrice->description): ?>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Description</dt>
                        <dd class="text-sm text-gray-900 bg-gray-50 p-4 rounded-md"><?php echo e($unitPrice->description); ?></dd>
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

            <!-- Usage Statistics -->
            <?php if($unitPrice->isCurrent()): ?>
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
                    <h3 class="text-lg font-medium text-gray-900">Current Usage Statistics</h3>
                    <p class="mt-1 text-sm text-gray-600">Statistics for meters using this utility type</p>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div class="bg-gray-50 px-4 py-5 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Meters</dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                <?php echo e(\App\Models\UtilityMeter::where('type', $unitPrice->type)->where('status', 'active')->count()); ?>

                            </dd>
                        </div>

                        <div class="bg-blue-50 px-4 py-5 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 truncate">This Month Usage</dt>
                            <dd class="mt-1 text-3xl font-semibold text-blue-600">
                                <?php echo e(number_format(\App\Models\UtilityReading::whereHas('meter', function($q) use ($unitPrice) {
                                    $q->where('type', $unitPrice->type);
                                })->whereMonth('reading_date', now()->month)->sum('usage'), 2)); ?>

                            </dd>
                        </div>

                        <div class="bg-green-50 px-4 py-5 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 truncate">Revenue This Month</dt>
                            <dd class="mt-1 text-3xl font-semibold text-green-600">
                                <?php echo currency(\App\Models\UtilityReading::whereHas('meter', function($q) use ($unitPrice) {
                                    $q->where('type', $unitPrice->type);
                                })->whereMonth('reading_date', now()->month)->sum('amount')); ?>
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

            <!-- Price History -->
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
                    <h3 class="text-lg font-medium text-gray-900">Price History for <?php echo e(ucfirst($unitPrice->type)); ?></h3>
                    <p class="mt-1 text-sm text-gray-600">Historical pricing for this utility type</p>
                </div>

                <div class="overflow-hidden">
                    <?php
                        $priceHistory = \App\Models\UtilityUnitPrice::where('type', $unitPrice->type)
                            ->orderBy('effective_from', 'desc')
                            ->take(10)
                            ->get();
                    ?>

                    <?php if($priceHistory->count() > 0): ?>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective From</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Until</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $priceHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e($price->id === $unitPrice->id ? 'bg-blue-50' : 'hover:bg-gray-50'); ?>">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo currency($price->price_per_unit, 4); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($price->effective_from ? $price->effective_from->format('M j, Y') : 'Not set'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($price->effective_to ? $price->effective_to->format('M j, Y') : 'Ongoing'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        <?php echo e($price->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                                        <?php echo e(ucfirst($price->status)); ?>

                                    </span>
                                    <?php if($price->isCurrent()): ?>
                                    <div class="text-xs text-blue-600 mt-1">Current</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if($price->id !== $unitPrice->id): ?>
                                        <a href="<?php echo e(route('utility-unit-prices.show', $price)); ?>" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                    <?php else: ?>
                                        <span class="text-blue-600 font-medium">Current</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
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
<?php /**PATH F:\APPS\altezza\altezza\altezza\resources\views/utilities/unit-prices/show.blade.php ENDPATH**/ ?>