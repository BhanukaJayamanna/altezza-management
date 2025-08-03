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
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">System Settings</h2>
                            <p class="mt-1 text-sm text-gray-600">Configure your property management system</p>
                        </div>
                        <div class="flex space-x-3">
                            <!-- Export Button -->
                            <a href="<?php echo e(route('settings.export')); ?>" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export Settings
                            </a>
                            
                            <!-- Import Button -->
                            <button type="button" onclick="document.getElementById('import-modal').classList.remove('hidden')"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                                Import Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Form -->
            <form method="POST" action="<?php echo e(route('settings.update')); ?>" class="space-y-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <?php $__currentLoopData = $settingsGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $settings): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 capitalize">
                                    <?php echo e(str_replace('_', ' ', $groupName)); ?> Settings
                                </h3>
                                <p class="text-sm text-gray-600">Configure <?php echo e(str_replace('_', ' ', $groupName)); ?> related options</p>
                            </div>
                            <button type="button" 
                                    onclick="resetGroup('<?php echo e($groupName); ?>')"
                                    class="text-sm text-red-600 hover:text-red-800 font-medium">
                                Reset to Defaults
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="space-y-2">
                                <label for="<?php echo e($setting->key); ?>" class="block text-sm font-medium text-gray-700">
                                    <?php echo e(ucwords(str_replace('_', ' ', $setting->key))); ?>

                                    <?php if($setting->description): ?>
                                        <span class="text-xs text-gray-500 block"><?php echo e($setting->description); ?></span>
                                    <?php endif; ?>
                                </label>

                                <?php if($setting->type === 'boolean'): ?>
                                    <div class="flex items-center">
                                        <input type="hidden" name="settings[<?php echo e($setting->key); ?>]" value="0">
                                        <input type="checkbox" 
                                               id="<?php echo e($setting->key); ?>" 
                                               name="settings[<?php echo e($setting->key); ?>]" 
                                               value="1"
                                               <?php echo e($setting->value ? 'checked' : ''); ?>

                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="<?php echo e($setting->key); ?>" class="ml-2 text-sm text-gray-700">
                                            Enable <?php echo e(ucwords(str_replace('_', ' ', $setting->key))); ?>

                                        </label>
                                    </div>

                                <?php elseif($setting->type === 'password'): ?>
                                    <input type="password" 
                                           id="<?php echo e($setting->key); ?>" 
                                           name="settings[<?php echo e($setting->key); ?>]" 
                                           value="<?php echo e($setting->value); ?>"
                                           placeholder="Enter <?php echo e(str_replace('_', ' ', $setting->key)); ?>"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">

                                <?php elseif($setting->type === 'email'): ?>
                                    <input type="email" 
                                           id="<?php echo e($setting->key); ?>" 
                                           name="settings[<?php echo e($setting->key); ?>]" 
                                           value="<?php echo e($setting->value); ?>"
                                           placeholder="Enter email address"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">

                                <?php elseif($setting->type === 'url'): ?>
                                    <input type="url" 
                                           id="<?php echo e($setting->key); ?>" 
                                           name="settings[<?php echo e($setting->key); ?>]" 
                                           value="<?php echo e($setting->value); ?>"
                                           placeholder="https://example.com"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">

                                <?php elseif($setting->type === 'integer'): ?>
                                    <input type="number" 
                                           id="<?php echo e($setting->key); ?>" 
                                           name="settings[<?php echo e($setting->key); ?>]" 
                                           value="<?php echo e($setting->value); ?>"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">

                                <?php elseif($setting->type === 'decimal'): ?>
                                    <input type="number" 
                                           step="0.01"
                                           id="<?php echo e($setting->key); ?>" 
                                           name="settings[<?php echo e($setting->key); ?>]" 
                                           value="<?php echo e($setting->value); ?>"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">

                                <?php elseif($setting->key === 'currency'): ?>
                                    <select id="<?php echo e($setting->key); ?>" 
                                            name="settings[<?php echo e($setting->key); ?>]" 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                                        <option value="LKR" <?php echo e($setting->value === 'LKR' ? 'selected' : ''); ?>>LKR - Sri Lankan Rupee</option>
                                        <option value="USD" <?php echo e($setting->value === 'USD' ? 'selected' : ''); ?>>USD - US Dollar</option>
                                        <option value="EUR" <?php echo e($setting->value === 'EUR' ? 'selected' : ''); ?>>EUR - Euro</option>
                                        <option value="GBP" <?php echo e($setting->value === 'GBP' ? 'selected' : ''); ?>>GBP - British Pound</option>
                                        <option value="INR" <?php echo e($setting->value === 'INR' ? 'selected' : ''); ?>>INR - Indian Rupee</option>
                                        <option value="AUD" <?php echo e($setting->value === 'AUD' ? 'selected' : ''); ?>>AUD - Australian Dollar</option>
                                        <option value="CAD" <?php echo e($setting->value === 'CAD' ? 'selected' : ''); ?>>CAD - Canadian Dollar</option>
                                    </select>

                                <?php elseif($setting->key === 'currency_symbol'): ?>
                                    <select id="<?php echo e($setting->key); ?>" 
                                            name="settings[<?php echo e($setting->key); ?>]" 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                                        <option value="Rs." <?php echo e($setting->value === 'Rs.' ? 'selected' : ''); ?>>Rs. (Sri Lankan Rupee)</option>
                                        <option value="$" <?php echo e($setting->value === '$' ? 'selected' : ''); ?>>$ (US Dollar)</option>
                                        <option value="€" <?php echo e($setting->value === '€' ? 'selected' : ''); ?>>€ (Euro)</option>
                                        <option value="£" <?php echo e($setting->value === '£' ? 'selected' : ''); ?>>£ (British Pound)</option>
                                        <option value="₹" <?php echo e($setting->value === '₹' ? 'selected' : ''); ?>>₹ (Indian Rupee)</option>
                                        <option value="A$" <?php echo e($setting->value === 'A$' ? 'selected' : ''); ?>>A$ (Australian Dollar)</option>
                                        <option value="C$" <?php echo e($setting->value === 'C$' ? 'selected' : ''); ?>>C$ (Canadian Dollar)</option>
                                    </select>

                                <?php elseif($setting->key === 'timezone'): ?>
                                    <select id="<?php echo e($setting->key); ?>" 
                                            name="settings[<?php echo e($setting->key); ?>]" 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                                        <option value="Asia/Colombo" <?php echo e($setting->value === 'Asia/Colombo' ? 'selected' : ''); ?>>Asia/Colombo (Sri Lanka)</option>
                                        <option value="UTC" <?php echo e($setting->value === 'UTC' ? 'selected' : ''); ?>>UTC</option>
                                        <option value="America/New_York" <?php echo e($setting->value === 'America/New_York' ? 'selected' : ''); ?>>America/New_York</option>
                                        <option value="Europe/London" <?php echo e($setting->value === 'Europe/London' ? 'selected' : ''); ?>>Europe/London</option>
                                        <option value="Asia/Dubai" <?php echo e($setting->value === 'Asia/Dubai' ? 'selected' : ''); ?>>Asia/Dubai</option>
                                        <option value="Asia/Karachi" <?php echo e($setting->value === 'Asia/Karachi' ? 'selected' : ''); ?>>Asia/Karachi</option>
                                        <option value="Asia/Kolkata" <?php echo e($setting->value === 'Asia/Kolkata' ? 'selected' : ''); ?>>Asia/Kolkata</option>
                                        <option value="Australia/Sydney" <?php echo e($setting->value === 'Australia/Sydney' ? 'selected' : ''); ?>>Australia/Sydney</option>
                                    </select>

                                <?php else: ?>
                                    <input type="text" 
                                           id="<?php echo e($setting->key); ?>" 
                                           name="settings[<?php echo e($setting->key); ?>]" 
                                           value="<?php echo e($setting->value); ?>"
                                           placeholder="Enter <?php echo e(str_replace('_', ' ', $setting->key)); ?>"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                                <?php endif; ?>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Action Buttons -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex space-x-3">
                                <button type="submit"
                                        class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Save All Settings
                                </button>

                                <?php if(isset($settingsGroups['email'])): ?>
                                <button type="button" 
                                        onclick="testEmail()"
                                        class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Test Email
                                </button>
                                <?php endif; ?>
                            </div>

                            <div class="flex space-x-3">
                                <button type="button" 
                                        onclick="resetAllSettings()"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Reset All to Defaults
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="import-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Import Settings</h3>
                    <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="<?php echo e(route('settings.import')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="space-y-4">
                        <div>
                            <label for="settings_file" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Settings File (JSON)
                            </label>
                            <input type="file" 
                                   id="settings_file" 
                                   name="settings_file" 
                                   accept=".json"
                                   required
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm text-yellow-700">
                                    Importing settings will overwrite existing values. Make sure to export current settings as backup first.
                                </p>
                            </div>
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button type="submit"
                                    class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Import Settings
                            </button>
                            <button type="button" 
                                    onclick="document.getElementById('import-modal').classList.add('hidden')"
                                    class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        function resetGroup(group) {
            if (confirm(`Are you sure you want to reset ${group.replace('_', ' ')} settings to defaults? This action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo e(route("settings.reset")); ?>';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '<?php echo e(csrf_token()); ?>';
                
                const groupInput = document.createElement('input');
                groupInput.type = 'hidden';
                groupInput.name = 'group';
                groupInput.value = group;
                
                form.appendChild(csrfToken);
                form.appendChild(groupInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function resetAllSettings() {
            if (confirm('Are you sure you want to reset ALL settings to defaults? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo e(route("settings.reset")); ?>';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '<?php echo e(csrf_token()); ?>';
                
                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function testEmail() {
            if (confirm('This will send a test email using current email settings. Continue?')) {
                fetch('<?php echo e(route("settings.test-email")); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message || 'Test email sent successfully');
                })
                .catch(error => {
                    alert('Failed to send test email: ' + error.message);
                });
            }
        }

        // Auto-save draft functionality
        let autoSaveTimeout;
        const inputs = document.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => {
                    // Save to localStorage as draft
                    const formData = new FormData(document.querySelector('form'));
                    const settings = {};
                    for (let [key, value] of formData.entries()) {
                        if (key.startsWith('settings[')) {
                            settings[key] = value;
                        }
                    }
                    localStorage.setItem('altezza_settings_draft', JSON.stringify(settings));
                }, 1000);
            });
        });

        // Load draft on page load
        window.addEventListener('load', function() {
            const draft = localStorage.getItem('altezza_settings_draft');
            if (draft && confirm('Found unsaved changes. Would you like to restore them?')) {
                const settings = JSON.parse(draft);
                for (let [key, value] of Object.entries(settings)) {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = value === '1';
                        } else {
                            input.value = value;
                        }
                    }
                }
            }
        });

        // Clear draft on successful save
        document.querySelector('form').addEventListener('submit', function() {
            localStorage.removeItem('altezza_settings_draft');
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
<?php /**PATH F:\APPS\altezza\altezza\altezza\resources\views/settings/index.blade.php ENDPATH**/ ?>