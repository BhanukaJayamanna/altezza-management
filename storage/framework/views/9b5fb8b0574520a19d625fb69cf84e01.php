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
                <?php echo e(__('Utility Reading Details')); ?>

            </h2>
            <div class="flex space-x-3">
                <a href="<?php echo e(route('utility-readings.edit', $reading)); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Edit Reading
                </a>
                <a href="<?php echo e(route('utility-readings.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to Readings
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            
            <!-- Reading Details -->
            <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'mb-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-6']); ?>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Reading Information</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        <?php echo e(ucfirst($reading->meter->type)); ?> reading for <?php echo e($reading->meter->apartment->number); ?>

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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?php echo e($reading->meter->type === 'electricity' ? 'yellow' : ($reading->meter->type === 'water' ? 'blue' : 'orange')); ?>-100 text-<?php echo e($reading->meter->type === 'electricity' ? 'yellow' : ($reading->meter->type === 'water' ? 'blue' : 'orange')); ?>-800">
                                            <?php echo e(ucfirst($reading->meter->type)); ?>

                                        </span>
                                        <span><?php echo e($reading->meter->apartment->number); ?> - <?php echo e($reading->meter->meter_number); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Reading Date</label>
                                <p class="mt-1 text-sm text-gray-900"><?php echo e($reading->reading_date->format('M j, Y')); ?></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Billing Period</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <?php echo e($reading->billing_period_start->format('M j, Y')); ?> - <?php echo e($reading->billing_period_end->format('M j, Y')); ?>

                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Recorded By</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <?php echo e($reading->recordedBy ? $reading->recordedBy->name : 'System'); ?>

                                </p>
                            </div>
                        </div>

                        <!-- Reading Values -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Previous Reading</label>
                                <p class="mt-1 text-lg font-semibold text-gray-900"><?php echo e(number_format($reading->previous_reading, 2)); ?></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Current Reading</label>
                                <p class="mt-1 text-lg font-semibold text-gray-900"><?php echo e(number_format($reading->current_reading, 2)); ?></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Consumption</label>
                                <p class="mt-1 text-2xl font-bold text-blue-600">
                                    <?php echo e(number_format($reading->consumption, 2)); ?>

                                    <span class="text-sm font-normal text-gray-500">
                                        <?php echo e($reading->meter->type === 'electricity' ? 'kWh' : ($reading->meter->type === 'water' ? 'gallons' : 'cubic feet')); ?>

                                    </span>
                                </p>
                            </div>

                            <?php if($reading->amount): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Calculated Amount</label>
                                <p class="mt-1 text-2xl font-bold text-green-600"><?php echo currency($reading->amount); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if($reading->notes): ?>
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded-md">
                            <p class="text-sm text-gray-900"><?php echo e($reading->notes); ?></p>
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

            <!-- Associated Bills -->
            <?php if($reading->bill): ?>
            <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'mb-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-6']); ?>
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Associated Bill</h3>
                </div>

                <div class="p-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">
                                Bill #<?php echo e($reading->bill->bill_number); ?>

                            </h4>
                            <p class="text-sm text-gray-600">
                                Due: <?php echo e($reading->bill->due_date->format('M j, Y')); ?>

                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-semibold text-gray-900">
                                <?php echo currency($reading->bill->total_amount); ?>
                            </p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                <?php echo e($reading->bill->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                   ($reading->bill->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')); ?>">
                                <?php echo e(ucfirst($reading->bill->status)); ?>

                            </span>
                        </div>
                        <div>
                            <a href="<?php echo e(route('utility-bills.show', $reading->bill)); ?>" 
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                                View Bill
                            </a>
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

            <!-- Actions -->
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
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                </div>

                <div class="p-6">
                    <div class="flex space-x-3">
                        <a href="<?php echo e(route('utility-readings.edit', $reading)); ?>" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Edit Reading
                        </a>

                        <?php if (! ($reading->bill)): ?>
                        <form method="POST" action="<?php echo e(route('utility-readings.destroy', $reading)); ?>" 
                              class="inline-block"
                              onsubmit="return confirm('Are you sure you want to delete this reading? This action cannot be undone.')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                Delete Reading
                            </button>
                        </form>
                        <?php endif; ?>

                        <?php if(!$reading->bill): ?>
                        <form method="POST" action="<?php echo e(route('utility-readings.generate-bills')); ?>" class="inline-block">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="reading_ids[]" value="<?php echo e($reading->id); ?>">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Generate Bill
                            </button>
                        </form>
                        <?php endif; ?>
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
<?php /**PATH F:\APPS\altezza\altezza\altezza\resources\views/utilities/readings/show.blade.php ENDPATH**/ ?>