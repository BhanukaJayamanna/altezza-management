<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tenant Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Welcome, {{ Auth::user()->name }}!</h3>
                            <p class="text-gray-600">Tenant Account</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</p>
                            <p class="text-sm text-gray-500">{{ now()->format('g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Apartment Assigned Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center py-12">
                        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-yellow-100 mb-6">
                            <svg class="h-12 w-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            No Apartment Assigned
                        </h3>
                        
                        <p class="text-gray-600 mb-6 max-w-md mx-auto">
                            Your tenant account has been created, but you haven't been assigned to an apartment yet. 
                            Please contact the property management to complete your apartment assignment.
                        </p>

                        <!-- Contact Information -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 max-w-md mx-auto">
                            <h4 class="text-sm font-medium text-blue-900 mb-3">Contact Property Management</h4>
                            <div class="space-y-2 text-sm text-blue-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>info@altezzaproperties.com</span>
                                </div>
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span>(555) 123-4567</span>
                                </div>
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Office Hours: Mon-Fri 9AM-5PM</span>
                                </div>
                            </div>
                        </div>

                        <!-- What Happens Next -->
                        <div class="mt-8">
                            <h4 class="text-sm font-medium text-gray-900 mb-4">What happens next?</h4>
                            <div class="max-w-md mx-auto text-left">
                                <div class="space-y-3">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-600">1</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Property management will assign you to an apartment</p>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-600">2</span>
                                        </div>
                                        <p class="text-sm text-gray-600">You'll receive an email notification when assignment is complete</p>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-600">3</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Once assigned, you'll have access to your full tenant portal</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Available Actions -->
                        <div class="mt-8">
                            <h4 class="text-sm font-medium text-gray-900 mb-4">What you can do now</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-md mx-auto">
                                <a href="{{ route('profile.edit') }}" 
                                   class="flex flex-col items-center p-4 border-2 border-gray-200 border-dashed rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
                                    <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">Update Profile</span>
                                </a>

                                <a href="{{ route('tenant.notices') }}" 
                                   class="flex flex-col items-center p-4 border-2 border-gray-200 border-dashed rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors">
                                    <svg class="w-8 h-8 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM8.515 2l5.801 5.998h5.684v5.002h-5.684L8.515 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">View Notices</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Status -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Account Status</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900">Account Created</h4>
                            <p class="text-xs text-gray-500 mt-1">Your tenant account is active</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900">Pending Assignment</h4>
                            <p class="text-xs text-gray-500 mt-1">Waiting for apartment assignment</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-medium text-gray-400">Full Portal Access</h4>
                            <p class="text-xs text-gray-400 mt-1">Available after assignment</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
