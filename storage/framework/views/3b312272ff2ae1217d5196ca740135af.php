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
                <?php echo e(__('Add Utility Reading')); ?>

            </h2>
            <a href="<?php echo e(route('utility-readings.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Readings
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <?php if($errors->any()): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

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
                    <h3 class="text-lg font-medium text-gray-900">Reading Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Enter the meter reading details.</p>
                </div>

                <form method="POST" action="<?php echo e(route('utility-readings.store')); ?>" class="p-6 space-y-6">
                    <?php echo csrf_field(); ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Meter Selection -->
                        <div>
                            <label for="meter_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Meter <span class="text-red-500">*</span>
                            </label>
                            <select id="meter_id" name="meter_id" required onchange="updateMeterInfo()"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Meter</option>
                                <?php $__currentLoopData = $meters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($meter->id); ?>" 
                                            data-type="<?php echo e($meter->type); ?>"
                                            data-apartment="<?php echo e($meter->apartment->number); ?>"
                                            data-last-reading="<?php echo e($meter->last_reading); ?>"
                                            data-last-date="<?php echo e($meter->last_reading_date?->format('Y-m-d')); ?>"
                                            <?php echo e(old('meter_id') == $meter->id ? 'selected' : ''); ?>>
                                        <?php echo e($meter->apartment->number); ?> - <?php echo e(ucfirst($meter->type)); ?> (<?php echo e($meter->meter_number); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div id="meter-info" class="mt-1 text-sm text-gray-500"></div>
                        </div>

                        <!-- Reading Date -->
                        <div>
                            <label for="reading_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Reading Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="reading_date" name="reading_date" 
                                   value="<?php echo e(old('reading_date', now()->format('Y-m-d'))); ?>" 
                                   max="<?php echo e(now()->format('Y-m-d')); ?>" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Current Reading -->
                        <div>
                            <label for="current_reading" class="block text-sm font-medium text-gray-700 mb-2">
                                Current Reading <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="current_reading" name="current_reading" 
                                   value="<?php echo e(old('current_reading')); ?>" 
                                   step="0.01" min="0" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0.00">
                            <p class="mt-1 text-sm text-gray-500">Enter the current meter reading value</p>
                        </div>

                        <!-- Previous Reading (Display Only) -->
                        <div>
                            <label for="previous_reading_display" class="block text-sm font-medium text-gray-700 mb-2">
                                Previous Reading
                            </label>
                            <input type="text" id="previous_reading_display" readonly
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500"
                                   placeholder="Select meter to see previous reading">
                        </div>

                        <!-- Billing Period Start -->
                        <div>
                            <label for="billing_period_start" class="block text-sm font-medium text-gray-700 mb-2">
                                Billing Period Start <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="billing_period_start" name="billing_period_start" 
                                   value="<?php echo e(old('billing_period_start', now()->startOfMonth()->format('Y-m-d'))); ?>" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Billing Period End -->
                        <div>
                            <label for="billing_period_end" class="block text-sm font-medium text-gray-700 mb-2">
                                Billing Period End <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="billing_period_end" name="billing_period_end" 
                                   value="<?php echo e(old('billing_period_end', now()->endOfMonth()->format('Y-m-d'))); ?>" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Consumption Display -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Consumption Calculation</h4>
                        <div id="consumption-display" class="text-lg font-semibold text-blue-700">
                            Enter current reading to calculate consumption
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any additional notes about this reading..."><?php echo e(old('notes')); ?></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <a href="<?php echo e(route('utility-readings.index')); ?>" 
                           class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit']); ?>
                            Add Reading
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    </div>
                </form>
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

    <?php $__env->startPush('scripts'); ?>
    <script>
        function updateMeterInfo() {
            const select = document.getElementById('meter_id');
            const meterInfo = document.getElementById('meter-info');
            const previousReadingDisplay = document.getElementById('previous_reading_display');
            const consumptionDisplay = document.getElementById('consumption-display');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                const type = selectedOption.dataset.type;
                const apartment = selectedOption.dataset.apartment;
                const lastReading = selectedOption.dataset.lastReading;
                const lastDate = selectedOption.dataset.lastDate;
                
                meterInfo.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${type === 'electricity' ? 'yellow' : (type === 'water' ? 'blue' : 'orange')}-100 text-${type === 'electricity' ? 'yellow' : (type === 'water' ? 'blue' : 'orange')}-800">
                            ${type.charAt(0).toUpperCase() + type.slice(1)}
                        </span>
                        <span>Apartment ${apartment}</span>
                    </div>
                `;
                
                if (lastReading) {
                    previousReadingDisplay.value = `${parseFloat(lastReading).toFixed(2)} (${lastDate || 'Unknown date'})`;
                } else {
                    previousReadingDisplay.value = '0.00 (No previous reading)';
                }
                
                calculateConsumption();
            } else {
                meterInfo.textContent = '';
                previousReadingDisplay.value = '';
                consumptionDisplay.textContent = 'Enter current reading to calculate consumption';
            }
        }

        function calculateConsumption() {
            const select = document.getElementById('meter_id');
            const currentReading = parseFloat(document.getElementById('current_reading').value) || 0;
            const consumptionDisplay = document.getElementById('consumption-display');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value && currentReading > 0) {
                const previousReading = parseFloat(selectedOption.dataset.lastReading) || 0;
                const consumption = currentReading - previousReading;
                const type = selectedOption.dataset.type;
                const unit = type === 'electricity' ? 'kWh' : (type === 'water' ? 'gallons' : 'cubic feet');
                
                if (consumption >= 0) {
                    consumptionDisplay.innerHTML = `
                        <div class="flex items-center justify-between">
                            <span>Consumption: <strong>${consumption.toFixed(2)} ${unit}</strong></span>
                            <span class="text-sm">(${currentReading.toFixed(2)} - ${previousReading.toFixed(2)})</span>
                        </div>
                    `;
                } else {
                    consumptionDisplay.innerHTML = `
                        <div class="text-red-600">
                            <strong>Warning:</strong> Current reading is less than previous reading!
                        </div>
                    `;
                }
            } else {
                consumptionDisplay.textContent = 'Enter current reading to calculate consumption';
            }
        }

        // Event listeners
        document.getElementById('current_reading').addEventListener('input', calculateConsumption);
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateMeterInfo();
        });
    </script>
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
<?php /**PATH F:\APPS\altezza\altezza\altezza\resources\views/utilities/readings/create.blade.php ENDPATH**/ ?>