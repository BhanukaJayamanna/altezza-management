<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                    Toast Notification System Demo
                </h2>
                <p class="text-slate-600 text-sm mt-1">Test and configure the modern toast notification system.</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 text-xs bg-orange-100 text-orange-700 rounded-full font-medium">Development</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            
            <!-- Server-side Toast Tests -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Server-side Flash Messages</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <form method="POST" action="{{ route('toast.test') }}">
                        @csrf
                        <input type="hidden" name="type" value="success">
                        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                            Success Toast
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('toast.test') }}">
                        @csrf
                        <input type="hidden" name="type" value="error">
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                            Error Toast
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('toast.test') }}">
                        @csrf
                        <input type="hidden" name="type" value="warning">
                        <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors">
                            Warning Toast
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('toast.test') }}">
                        @csrf
                        <input type="hidden" name="type" value="info">
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                            Info Toast
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Client-side Toast Tests -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Client-side JavaScript Toasts</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <button onclick="showSuccess('Operation completed successfully!', 'Great!')" 
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                        JS Success
                    </button>
                    
                    <button onclick="showError('Something went wrong!', 'Oops!')" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                        JS Error
                    </button>
                    
                    <button onclick="showWarning('Please check your input!', 'Attention!')" 
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors">
                        JS Warning
                    </button>
                    
                    <button onclick="showInfo('This is some useful information.', 'FYI')" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                        JS Info
                    </button>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <button onclick="showLoadingToast()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Loading Toast
                    </button>
                    
                    <button onclick="testActionToast()" 
                            class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Action Toast
                    </button>
                    
                    <button onclick="testBatchToasts()" 
                            class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Batch Toasts
                    </button>
                </div>
            </div>
            
            <!-- Advanced Features -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Advanced Features</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <button onclick="testPromiseToast()" 
                            class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Promise Toast
                    </button>
                    
                    <button onclick="testPersistentToast()" 
                            class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Persistent Toast
                    </button>
                    
                    <button onclick="clearAllToasts()" 
                            class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg transition-colors">
                        Clear All
                    </button>
                    
                    <button onclick="togglePosition()" 
                            class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Toggle Position
                    </button>
                </div>
            </div>
            
            <!-- Configuration Panel -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Configuration</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                            <select id="positionSelect" onchange="changePosition(this.value)" 
                                    class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="top-right">Top Right</option>
                                <option value="top-left">Top Left</option>
                                <option value="bottom-right">Bottom Right</option>
                                <option value="bottom-left">Bottom Left</option>
                                <option value="top-center">Top Center</option>
                                <option value="bottom-center">Bottom Center</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Toasts</label>
                            <input type="number" id="maxToasts" value="5" min="1" max="10" 
                                   onchange="changeMaxToasts(parseInt(this.value))"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sound</label>
                            <button id="soundToggle" onclick="toggleSound()" 
                                    class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors">
                                Sound: OFF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Property Management Examples -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Property Management Examples</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button onclick="simulateRentPayment()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition-colors text-left">
                        <div class="font-medium">Rent Payment Received</div>
                        <div class="text-sm opacity-90">Simulate successful payment</div>
                    </button>
                    
                    <button onclick="simulateMaintenanceRequest()" 
                            class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-3 rounded-lg transition-colors text-left">
                        <div class="font-medium">Maintenance Request</div>
                        <div class="text-sm opacity-90">New urgent request</div>
                    </button>
                    
                    <button onclick="simulateLeaseExpiry()" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg transition-colors text-left">
                        <div class="font-medium">Lease Expiry Warning</div>
                        <div class="text-sm opacity-90">Lease expires soon</div>
                    </button>
                    
                    <button onclick="simulateInvoiceGenerated()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition-colors text-left">
                        <div class="font-medium">Invoice Generated</div>
                        <div class="text-sm opacity-90">Monthly invoice ready</div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Demo functions
    let currentPosition = 'top-right';
    let soundEnabled = false;

    // Wait for altezzaToast to be available
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure altezzaToast is loaded
        if (typeof altezzaToast === 'undefined') {
            console.error('AltezzaToast not loaded');
            return;
        }
    });

    function showLoadingToast() {
        if (typeof showLoading !== 'undefined') {
            showLoading('Processing your request...', 'Please wait');
        }
    }

    function testActionToast() {
        if (typeof altezzaToast !== 'undefined') {
            altezzaToast.show({
                type: 'info',
                title: 'Action Required',
                message: 'Would you like to view the settings page?',
                timeout: 0,
                action: {
                    label: 'Go to Settings',
                    url: '{{ route("settings.index") }}'
                }
            });
        }
    }

    function testBatchToasts() {
        if (typeof altezzaToast !== 'undefined') {
            const toasts = [
                { type: 'info', title: 'Step 1', message: 'Initializing process...' },
                { type: 'info', title: 'Step 2', message: 'Validating data...' },
                { type: 'info', title: 'Step 3', message: 'Processing request...' },
                { type: 'success', title: 'Complete!', message: 'All steps completed successfully.' }
            ];
            
            altezzaToast.showBatch(toasts, 500);
        }
    }

    function testPromiseToast() {
        if (typeof altezzaToast !== 'undefined') {
            const simulateApi = new Promise((resolve) => {
                setTimeout(() => resolve('API call completed'), 3000);
            });
            
            altezzaToast.loadingPromise(
                simulateApi,
                'Calling API...',
                'API call successful!',
                'API call failed!'
            );
        }
    }

    function testPersistentToast() {
        if (typeof altezzaToast !== 'undefined') {
            altezzaToast.show({
                type: 'warning',
                title: 'Important Notice',
                message: 'This toast will not auto-dismiss. Click the X to close it.',
                timeout: 0,
                persistent: true
            });
        }
    }

    function togglePosition() {
        if (typeof altezzaToast !== 'undefined') {
            const positions = ['top-right', 'top-left', 'bottom-right', 'bottom-left', 'top-center', 'bottom-center'];
            const currentIndex = positions.indexOf(currentPosition);
            const nextIndex = (currentIndex + 1) % positions.length;
            currentPosition = positions[nextIndex];
            
            altezzaToast.setPosition(currentPosition);
            document.getElementById('positionSelect').value = currentPosition;
            
            showInfo(`Position changed to: ${currentPosition.replace('-', ' ')}`, 'Position Updated');
        }
    }

    function changePosition(position) {
        if (typeof altezzaToast !== 'undefined') {
            currentPosition = position;
            altezzaToast.setPosition(position);
        }
    }

    function changeMaxToasts(max) {
        if (typeof altezzaToast !== 'undefined') {
            altezzaToast.setMaxToasts(max);
        }
    }

    function toggleSound() {
        if (typeof altezzaToast !== 'undefined') {
            soundEnabled = !soundEnabled;
            altezzaToast.enableSound(soundEnabled);
            
            const button = document.getElementById('soundToggle');
            button.textContent = `Sound: ${soundEnabled ? 'ON' : 'OFF'}`;
            button.className = soundEnabled 
                ? 'w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors'
                : 'w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors';
        }
    }

    function clearAllToasts() {
        if (typeof altezzaToast !== 'undefined') {
            altezzaToast.clear();
        }
    }

    // Property Management Examples
    function simulateRentPayment() {
        if (typeof altezzaToast !== 'undefined') {
            altezzaToast.show({
                type: 'success',
                title: 'Payment Received',
                message: 'Rent payment of Rs. 25,000 received from Apartment 12A',
                timeout: 6000,
                action: {
                    label: 'View Receipt',
                    callback: () => alert('Opening receipt...')
                }
            });
        }
    }

    function simulateMaintenanceRequest() {
        if (typeof altezzaToast !== 'undefined') {
            altezzaToast.show({
                type: 'warning',
                title: 'Urgent Maintenance',
                message: 'Plumbing issue reported in Apartment 8B - requires immediate attention',
                timeout: 0,
                action: {
                    label: 'Assign Technician',
                    callback: () => alert('Assigning technician...')
                }
            });
        }
    }

    function simulateLeaseExpiry() {
        if (typeof altezzaToast !== 'undefined') {
            altezzaToast.show({
                type: 'error',
                title: 'Lease Expiry Alert',
                message: '3 leases expire within 30 days - renewal required',
                timeout: 0,
                action: {
                    label: 'View Leases',
                    callback: () => alert('Opening lease management...')
                }
            });
        }
    }

    function simulateInvoiceGenerated() {
        if (typeof altezzaToast !== 'undefined') {
            altezzaToast.show({
                type: 'info',
                title: 'Monthly Invoices Ready',
                message: '25 invoices generated for January 2025',
                timeout: 8000,
                action: {
                    label: 'Send Invoices',
                    callback: () => {
                        showLoading('Sending invoices...', 'Processing');
                        setTimeout(() => {
                            showSuccess('All invoices sent successfully!', 'Email Sent');
                        }, 2000);
                    }
                }
            });
        }
    }
    </script>
</x-app-layout>
