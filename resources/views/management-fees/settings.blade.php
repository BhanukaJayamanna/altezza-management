<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                    {{ __('Management Fee Settings') }}
                </h2>
                <p class="text-slate-600 text-sm mt-1">Configure ratios and billing settings for management fees</p>
            </div>
            <a href="{{ route('management-fees.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </x-slot>

<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Fee Configuration</h3>
                </div>
                <form method="POST" action="{{ route('management-fees.update-settings') }}" class="p-6">
                    @csrf
                    
                    <!-- Management Fee Ratio -->
                    <div class="mb-6">
                        <label for="management_fee_ratio" class="block text-sm font-medium text-gray-700 mb-2">
                            Management Fee Ratio (per square foot)
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="management_fee_ratio" 
                                   name="management_fee_ratio"
                                   step="0.01"
                                   min="0"
                                   max="999.99"
                                   value="{{ old('management_fee_ratio', $settings['management_fee_ratio']) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">{{ $settings['currency_symbol'] }}</span>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Current rate: {{ $settings['currency_symbol'] }}{{ $settings['management_fee_ratio'] }} per sqft/month
                        </p>
                        @error('management_fee_ratio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sinking Fund Ratio -->
                    <div class="mb-6">
                        <label for="sinking_fund_ratio" class="block text-sm font-medium text-gray-700 mb-2">
                            Sinking Fund Ratio (per square foot)
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="sinking_fund_ratio" 
                                   name="sinking_fund_ratio"
                                   step="0.01"
                                   min="0"
                                   max="999.99"
                                   value="{{ old('sinking_fund_ratio', $settings['sinking_fund_ratio']) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">{{ $settings['currency_symbol'] }}</span>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Current rate: {{ $settings['currency_symbol'] }}{{ $settings['sinking_fund_ratio'] }} per sqft/month
                        </p>
                        @error('sinking_fund_ratio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Days -->
                    <div class="mb-6">
                        <label for="due_days" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Due Days
                        </label>
                        <input type="number" 
                               id="due_days" 
                               name="due_days"
                               min="1"
                               max="365"
                               value="{{ old('due_days', $settings['due_days']) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        <p class="mt-1 text-sm text-gray-500">
                            Number of days after invoice generation when payment is due
                        </p>
                        @error('due_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Late Fee Percentage -->
                    <div class="mb-6">
                        <label for="late_fee_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                            Late Fee Percentage
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="late_fee_percentage" 
                                   name="late_fee_percentage"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   value="{{ old('late_fee_percentage', $settings['late_fee_percentage']) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Percentage applied to overdue invoices as late fee
                        </p>
                        @error('late_fee_percentage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Auto Generate -->
                    <div class="mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="auto_generate" 
                                   name="auto_generate"
                                   value="1"
                                   {{ old('auto_generate', $settings['auto_generate']) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="auto_generate" class="ml-2 block text-sm text-gray-700">
                                Auto-generate management fees for new apartments
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Automatically create management fee records when new apartments are added
                        </p>
                    </div>

                    <!-- Update Existing Checkbox -->
                    <div class="mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="update_existing" 
                                   name="update_existing"
                                   value="1"
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <label for="update_existing" class="ml-2 block text-sm text-gray-700">
                                Update existing apartment management fees with new ratios
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-red-500">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            This will update all existing apartments with the new ratios. Use with caution.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-save mr-2"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Information Panel -->
        <div class="space-y-6">
            <!-- Current Calculation Preview -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Calculation Preview</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">For 1000 sqft apartment:</h4>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Management Fee (Monthly):</span>
                                    <span class="font-medium">1000 × {{ $settings['management_fee_ratio'] }} = {{ $settings['currency_symbol'] }}{{ number_format(1000 * $settings['management_fee_ratio'], 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Sinking Fund (Monthly):</span>
                                    <span class="font-medium">1000 × {{ $settings['sinking_fund_ratio'] }} = {{ $settings['currency_symbol'] }}{{ number_format(1000 * $settings['sinking_fund_ratio'], 2) }}</span>
                                </div>
                                
                                <div class="border-t pt-2">
                                    <div class="flex justify-between font-semibold">
                                        <span class="text-gray-900">Quarterly Total:</span>
                                        <span class="text-blue-600">{{ $settings['currency_symbol'] }}{{ number_format((1000 * $settings['management_fee_ratio'] + 1000 * $settings['sinking_fund_ratio']) * 3, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formula Explanation -->
            <div class="bg-blue-50 rounded-lg border border-blue-200">
                <div class="p-6">
                    <h4 class="font-semibold text-blue-900 mb-3">
                        <i class="fas fa-info-circle mr-2"></i>Calculation Formula
                    </h4>
                    <div class="space-y-3 text-sm text-blue-800">
                        <div>
                            <strong>Management Fee:</strong><br>
                            Area (sqft) × Management Ratio × 3 months
                        </div>
                        <div>
                            <strong>Sinking Fund:</strong><br>
                            Area (sqft) × Sinking Fund Ratio × 3 months
                        </div>
                        <div>
                            <strong>Total Quarterly Rental:</strong><br>
                            Management Fee + Sinking Fund
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="p-6">
                    <h4 class="font-semibold text-yellow-900 mb-3">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Important Notes
                    </h4>
                    <ul class="space-y-2 text-sm text-yellow-800">
                        <li>• Ratios are applied monthly and multiplied by 3 for quarterly billing</li>
                        <li>• Changes to ratios can be applied to existing apartments</li>
                        <li>• Late fees are calculated as a percentage of the total amount</li>
                        <li>• All calculations are rounded to 2 decimal places</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const managementRatioInput = document.getElementById('management_fee_ratio');
    const sinkingRatioInput = document.getElementById('sinking_fund_ratio');
    
    function updatePreview() {
        const managementRatio = parseFloat(managementRatioInput.value) || 0;
        const sinkingRatio = parseFloat(sinkingRatioInput.value) || 0;
        const area = 1000;
        
        const monthlyManagement = area * managementRatio;
        const monthlySinking = area * sinkingRatio;
        const quarterlyTotal = (monthlyManagement + monthlySinking) * 3;
        
        // Update preview values
        document.querySelector('.preview-management').textContent = 
            `1000 × ${managementRatio.toFixed(2)} = {{ $settings['currency_symbol'] }}${monthlyManagement.toFixed(2)}`;
        document.querySelector('.preview-sinking').textContent = 
            `1000 × ${sinkingRatio.toFixed(2)} = {{ $settings['currency_symbol'] }}${monthlySinking.toFixed(2)}`;
        document.querySelector('.preview-total').textContent = 
            `{{ $settings['currency_symbol'] }}${quarterlyTotal.toFixed(2)}`;
    }
    
    managementRatioInput.addEventListener('input', updatePreview);
    sinkingRatioInput.addEventListener('input', updatePreview);
});
</script>
    </div>
</div>
</x-app-layout>
