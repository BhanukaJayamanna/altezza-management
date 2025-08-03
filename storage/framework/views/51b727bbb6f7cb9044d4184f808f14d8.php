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
                <?php echo e(__('Create Invoice')); ?>

            </h2>
            <a href="<?php echo e(route('invoices.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Invoices
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="<?php echo e(route('invoices.store')); ?>">
                        <?php echo csrf_field(); ?>

                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Apartment -->
                                <div>
                                    <label for="apartment_id" class="block font-medium text-sm text-gray-700 mb-2">
                                        Apartment <span class="text-red-500">*</span>
                                    </label>
                                    <select id="apartment_id" name="apartment_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['apartment_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required onchange="updateTenant()">
                                        <option value="">Select Apartment</option>
                                        <?php $__currentLoopData = $apartments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $apartment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($apartment->id); ?>" 
                                                    data-tenant-id="<?php echo e($apartment->tenant?->id); ?>"
                                                    data-rent="<?php echo e($apartment->rent_amount); ?>"
                                                    <?php echo e(old('apartment_id') == $apartment->id ? 'selected' : ''); ?>>
                                                <?php echo e($apartment->number); ?> 
                                                <?php if($apartment->block): ?> - Block <?php echo e($apartment->block); ?> <?php endif; ?>
                                                (<?php echo e($apartment->tenant?->name ?? 'No Tenant'); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['apartment_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Tenant -->
                                <div>
                                    <label for="tenant_id" class="block font-medium text-sm text-gray-700 mb-2">
                                        Tenant <span class="text-red-500">*</span>
                                    </label>
                                    <select id="tenant_id" name="tenant_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['tenant_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                        <option value="">Select Tenant</option>
                                        <?php $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($tenant->id); ?>" <?php echo e(old('tenant_id') == $tenant->id ? 'selected' : ''); ?>>
                                                <?php echo e($tenant->name); ?> (<?php echo e($tenant->email); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['tenant_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Lease (Optional) -->
                                <div>
                                    <label for="lease_id" class="block font-medium text-sm text-gray-700 mb-2">
                                        Related Lease (Optional)
                                    </label>
                                    <select id="lease_id" name="lease_id" onchange="updateFromLease()" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['lease_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Select Lease (Optional)</option>
                                        <?php $__currentLoopData = $leases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lease): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($lease->id); ?>" 
                                                    data-apartment-id="<?php echo e($lease->apartment_id); ?>"
                                                    data-tenant-id="<?php echo e($lease->tenant_id); ?>"
                                                    data-rent="<?php echo e($lease->rent_amount); ?>"
                                                    <?php echo e(old('lease_id') == $lease->id ? 'selected' : ''); ?>>
                                                <?php echo e($lease->lease_number); ?> - Apt <?php echo e($lease->apartment->number); ?> (<?php echo e($lease->tenant->name); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['lease_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Type -->
                                <div>
                                    <label for="type" class="block font-medium text-sm text-gray-700 mb-2">
                                        Invoice Type <span class="text-red-500">*</span>
                                    </label>
                                    <select id="type" name="type" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required onchange="updateBaseAmount()">
                                        <option value="">Select Type</option>
                                        <option value="rent" <?php echo e(old('type') == 'rent' ? 'selected' : ''); ?>>Rent</option>
                                        <option value="utility" <?php echo e(old('type') == 'utility' ? 'selected' : ''); ?>>Utility</option>
                                        <option value="maintenance" <?php echo e(old('type') == 'maintenance' ? 'selected' : ''); ?>>Maintenance</option>
                                        <option value="other" <?php echo e(old('type') == 'other' ? 'selected' : ''); ?>>Other</option>
                                    </select>
                                    <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Due Date -->
                                <div>
                                    <label for="due_date" class="block font-medium text-sm text-gray-700 mb-2">
                                        Due Date <span class="text-red-500">*</span>
                                    </label>
                                    <input id="due_date" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           type="date" name="due_date" value="<?php echo e(old('due_date', date('Y-m-d', strtotime('+30 days')))); ?>" required />
                                    <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mt-6">
                                <label for="description" class="block font-medium text-sm text-gray-700 mb-2">
                                    Description
                                </label>
                                <textarea id="description" name="description" rows="3" 
                                          class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                          placeholder="Enter invoice description..."><?php echo e(old('description')); ?></textarea>
                                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <!-- Amount Details -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Amount Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Base Amount -->
                                <div>
                                    <label for="amount" class="block font-medium text-sm text-gray-700 mb-2">
                                        Amount ($) <span class="text-red-500">*</span>
                                    </label>
                                    <input id="amount" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           type="number" name="amount" value="<?php echo e(old('amount')); ?>" min="0" step="0.01" required onchange="calculateTotal()" />
                                    <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Late Fee -->
                                <div>
                                    <label for="late_fee" class="block font-medium text-sm text-gray-700 mb-2">
                                        Late Fee ($)
                                    </label>
                                    <input id="late_fee" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['late_fee'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           type="number" name="late_fee" value="<?php echo e(old('late_fee', 0)); ?>" min="0" step="0.01" onchange="calculateTotal()" />
                                    <?php $__errorArgs = ['late_fee'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Discount -->
                                <div>
                                    <label for="discount" class="block font-medium text-sm text-gray-700 mb-2">
                                        Discount ($)
                                    </label>
                                    <input id="discount" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['discount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           type="number" name="discount" value="<?php echo e(old('discount', 0)); ?>" min="0" step="0.01" onchange="calculateTotal()" />
                                    <?php $__errorArgs = ['discount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Billing Period -->
                                <div>
                                    <label for="billing_period_start" class="block font-medium text-sm text-gray-700 mb-2">
                                        Billing Period Start
                                    </label>
                                    <input id="billing_period_start" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['billing_period_start'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           type="date" name="billing_period_start" value="<?php echo e(old('billing_period_start')); ?>" />
                                    <?php $__errorArgs = ['billing_period_start'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <!-- Total Amount Display -->
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                                    <span id="total_display" class="text-2xl font-bold text-indigo-600">$0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Line Items (Optional) -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Line Items (Optional)</h3>
                                <button type="button" onclick="addLineItem()" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    + Add Item
                                </button>
                            </div>
                            <div id="line-items">
                                <!-- Line items will be added here dynamically -->
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex items-center space-x-4">
                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Create Invoice
                                </button>
                                
                                <a href="<?php echo e(route('invoices.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let lineItemCount = 0;

        function updateTenant() {
            const apartmentSelect = document.getElementById('apartment_id');
            const tenantSelect = document.getElementById('tenant_id');
            const selectedOption = apartmentSelect.options[apartmentSelect.selectedIndex];
            
            if (selectedOption.dataset.tenantId) {
                tenantSelect.value = selectedOption.dataset.tenantId;
            } else {
                tenantSelect.value = '';
            }
            
            updateBaseAmount();
        }

        function updateFromLease() {
            const leaseSelect = document.getElementById('lease_id');
            const apartmentSelect = document.getElementById('apartment_id');
            const tenantSelect = document.getElementById('tenant_id');
            const amountInput = document.getElementById('amount');
            const selectedOption = leaseSelect.options[leaseSelect.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                // Update apartment
                if (selectedOption.dataset.apartmentId) {
                    apartmentSelect.value = selectedOption.dataset.apartmentId;
                }
                
                // Update tenant
                if (selectedOption.dataset.tenantId) {
                    tenantSelect.value = selectedOption.dataset.tenantId;
                }
                
                // Update amount if it's a rent invoice
                const typeSelect = document.getElementById('type');
                if (typeSelect.value === 'rent' && selectedOption.dataset.rent) {
                    amountInput.value = selectedOption.dataset.rent;
                }
            }
        }

        function updateBaseAmount() {
            const apartmentSelect = document.getElementById('apartment_id');
            const typeSelect = document.getElementById('type');
            const amountInput = document.getElementById('amount');
            const selectedOption = apartmentSelect.options[apartmentSelect.selectedIndex];
            
            if (typeSelect.value === 'rent' && selectedOption.dataset.rent) {
                amountInput.value = selectedOption.dataset.rent;
            }
            
            calculateTotal();
        }

        function calculateTotal() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            const lateFee = parseFloat(document.getElementById('late_fee').value) || 0;
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            
            const total = amount + lateFee - discount;
            document.getElementById('total_display').textContent = '$' + total.toFixed(2);
        }

        function addLineItem() {
            lineItemCount++;
            const lineItemsContainer = document.getElementById('line-items');
            const lineItemHtml = `
                <div class="flex gap-4 mb-4 items-end" id="line-item-${lineItemCount}">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" name="items[${lineItemCount}][description]" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Item description">
                    </div>
                    <div class="w-32">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                        <input type="number" name="items[${lineItemCount}][amount]" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               min="0" step="0.01" placeholder="0.00">
                    </div>
                    <button type="button" onclick="removeLineItem(${lineItemCount})" 
                            class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 mb-1">
                        Remove
                    </button>
                </div>
            `;
            lineItemsContainer.insertAdjacentHTML('beforeend', lineItemHtml);
        }

        function removeLineItem(id) {
            document.getElementById(`line-item-${id}`).remove();
        }

        // Initialize total calculation
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
    </script>
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
<?php /**PATH F:\APPS\altezza\altezza\altezza\resources\views/invoices/create.blade.php ENDPATH**/ ?>