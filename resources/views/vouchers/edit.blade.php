<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Payment Voucher - {{ $voucher->voucher_number }}
            </h2>
            <a href="{{ route('vouchers.show', $voucher) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Voucher
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="p-6">
                    <form method="POST" action="{{ route('vouchers.update', $voucher) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="voucher_date" class="block text-sm font-medium text-gray-700">Voucher Date *</label>
                                    <input type="date" name="voucher_date" id="voucher_date" value="{{ old('voucher_date', $voucher->voucher_date->format('Y-m-d')) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('voucher_date') border-red-500 @enderror" required>
                                    @error('voucher_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="expense_category" class="block text-sm font-medium text-gray-700">Expense Category *</label>
                                    <select name="expense_category" id="expense_category" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('expense_category') border-red-500 @enderror" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $key => $label)
                                            <option value="{{ $key }}" {{ old('expense_category', $voucher->expense_category) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('expense_category')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount *</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">₹</span>
                                        </div>
                                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" value="{{ old('amount', $voucher->amount) }}" 
                                               class="pl-8 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('amount') border-red-500 @enderror" 
                                               placeholder="0.00" required>
                                    </div>
                                    @error('amount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="apartment_id" class="block text-sm font-medium text-gray-700">Related Apartment</label>
                                    <select name="apartment_id" id="apartment_id" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('apartment_id') border-red-500 @enderror">
                                        <option value="">Select Apartment (Optional)</option>
                                        @foreach($apartments as $apartment)
                                            <option value="{{ $apartment->id }}" {{ old('apartment_id', $voucher->apartment_id) == $apartment->id ? 'selected' : '' }}>
                                                {{ $apartment->number }} - {{ $apartment->type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('apartment_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Vendor Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Vendor Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="vendor_name" class="block text-sm font-medium text-gray-700">Vendor Name *</label>
                                    <input type="text" name="vendor_name" id="vendor_name" value="{{ old('vendor_name', $voucher->vendor_name) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('vendor_name') border-red-500 @enderror" 
                                           placeholder="Enter vendor name" required>
                                    @error('vendor_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="vendor_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                    <input type="text" name="vendor_phone" id="vendor_phone" value="{{ old('vendor_phone', $voucher->vendor_phone) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('vendor_phone') border-red-500 @enderror" 
                                           placeholder="Enter phone number">
                                    @error('vendor_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="vendor_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" name="vendor_email" id="vendor_email" value="{{ old('vendor_email', $voucher->vendor_email) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('vendor_email') border-red-500 @enderror" 
                                           placeholder="Enter email address">
                                    @error('vendor_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="vendor_address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <textarea name="vendor_address" id="vendor_address" rows="3" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('vendor_address') border-red-500 @enderror" 
                                              placeholder="Enter vendor address">{{ old('vendor_address', $voucher->vendor_address) }}</textarea>
                                    @error('vendor_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Payment Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method *</label>
                                    <select name="payment_method" id="payment_method" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('payment_method') border-red-500 @enderror" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="cash" {{ old('payment_method', $voucher->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="cheque" {{ old('payment_method', $voucher->payment_method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="bank_transfer" {{ old('payment_method', $voucher->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="online" {{ old('payment_method', $voucher->payment_method) == 'online' ? 'selected' : '' }}>Online Payment</option>
                                        <option value="card" {{ old('payment_method', $voucher->payment_method) == 'card' ? 'selected' : '' }}>Card Payment</option>
                                    </select>
                                    @error('payment_method')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="reference_number" class="block text-sm font-medium text-gray-700">Reference Number</label>
                                    <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number', $voucher->reference_number) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('reference_number') border-red-500 @enderror" 
                                           placeholder="Cheque number, transaction ID, etc.">
                                    <p class="mt-1 text-sm text-gray-500">Cheque number, transaction ID, or other reference</p>
                                    @error('reference_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Description and Attachments -->
                        <div class="pb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Description & Attachments</h3>
                            <div class="space-y-6">
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description / Purpose *</label>
                                    <textarea name="description" id="description" rows="4" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror" 
                                              placeholder="Describe the purpose of this payment..." required>{{ old('description', $voucher->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="receipt_file" class="block text-sm font-medium text-gray-700">Receipt / Invoice File</label>
                                    
                                    @if($voucher->receipt_file)
                                        <div class="mb-3 p-3 bg-gray-50 rounded-md">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <span class="text-sm text-gray-600">Current file attached</span>
                                                </div>
                                                <a href="{{ Storage::url($voucher->receipt_file) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-500">
                                                    View Current File
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="receipt_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>{{ $voucher->receipt_file ? 'Replace file' : 'Upload a file' }}</span>
                                                    <input id="receipt_file" name="receipt_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, PDF up to 5MB</p>
                                        </div>
                                    </div>
                                    @error('receipt_file')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('vouchers.show', $voucher) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Voucher
                            </button>
                        </div>
                    </form>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
