<!-- Toast Notification System -->
<div id="toast-container" 
     x-data="toastManager()" 
     x-init="initToasts()"
     class="fixed top-4 right-4 z-50 space-y-2 max-w-sm w-full">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show"
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transform ease-in duration-200 transition"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             :class="{
                'bg-green-500': toast.type === 'success',
                'bg-red-500': toast.type === 'error',
                'bg-yellow-500': toast.type === 'warning',
                'bg-blue-500': toast.type === 'info',
                'bg-gray-500': toast.type === 'loading'
             }"
             class="relative flex items-center p-4 rounded-lg shadow-lg text-white min-h-[60px]">
            
            <!-- Icon -->
            <div class="flex-shrink-0 mr-3">
                <!-- Success Icon -->
                <svg x-show="toast.type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                
                <!-- Error Icon -->
                <svg x-show="toast.type === 'error'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                
                <!-- Warning Icon -->
                <svg x-show="toast.type === 'warning'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                
                <!-- Info Icon -->
                <svg x-show="toast.type === 'info'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                
                <!-- Loading Icon -->
                <svg x-show="toast.type === 'loading'" class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                    <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
            
            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="font-medium text-sm" x-text="toast.title"></div>
                <div x-show="toast.message" class="text-sm opacity-90 mt-1" x-text="toast.message"></div>
            </div>
            
            <!-- Action Button (optional) -->
            <div x-show="toast.action" class="flex-shrink-0 ml-3">
                <button @click="toast.action.callback(); removeToast(toast.id)"
                        class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-1 rounded text-xs font-medium transition-colors duration-200"
                        x-text="toast.action.label">
                </button>
            </div>
            
            <!-- Close Button -->
            <button @click="removeToast(toast.id)" 
                    class="flex-shrink-0 ml-3 p-1 rounded-full hover:bg-white hover:bg-opacity-20 transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <!-- Progress Bar -->
            <div x-show="toast.timeout > 0" 
                 class="absolute bottom-0 left-0 h-1 bg-white bg-opacity-30 rounded-bl-lg rounded-br-lg"
                 :style="`width: ${toast.progress}%; transition: width ${toast.interval}ms linear;`">
            </div>
        </div>
    </template>
</div>

<script>
function toastManager() {
    return {
        toasts: [],
        nextId: 1,
        
        initToasts() {
            // Listen for custom toast events
            window.addEventListener('toast', (e) => {
                this.addToast(e.detail);
            });
        },
        
        addToast(options) {
            const toast = {
                id: this.nextId++,
                type: options.type || 'info',
                title: options.title || '',
                message: options.message || '',
                action: options.action || null,
                timeout: options.timeout || (options.type === 'error' ? 0 : 5000), // Errors don't auto-dismiss
                show: false,
                progress: 100,
                interval: 100
            };
            
            this.toasts.push(toast);
            
            // Trigger show animation
            this.$nextTick(() => {
                toast.show = true;
                
                // Start countdown if timeout is set
                if (toast.timeout > 0) {
                    this.startCountdown(toast);
                }
            });
            
            // Limit to 5 toasts maximum
            if (this.toasts.length > 5) {
                this.removeToast(this.toasts[0].id);
            }
        },
        
        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index > -1) {
                this.toasts[index].show = false;
                // Remove from array after animation
                setTimeout(() => {
                    this.toasts.splice(index, 1);
                }, 200);
            }
        },
        
        startCountdown(toast) {
            const totalTime = toast.timeout;
            let elapsedTime = 0;
            
            const timer = setInterval(() => {
                elapsedTime += toast.interval;
                toast.progress = Math.max(0, ((totalTime - elapsedTime) / totalTime) * 100);
                
                if (elapsedTime >= totalTime) {
                    clearInterval(timer);
                    this.removeToast(toast.id);
                }
            }, toast.interval);
        }
    }
}

// Global toast function for easy access
window.showToast = function(options) {
    window.dispatchEvent(new CustomEvent('toast', { detail: options }));
};

// Convenience functions
window.showSuccess = function(message, title = 'Success!') {
    window.showToast({ type: 'success', title, message });
};

window.showError = function(message, title = 'Error!') {
    window.showToast({ type: 'error', title, message });
};

window.showWarning = function(message, title = 'Warning!') {
    window.showToast({ type: 'warning', title, message });
};

window.showInfo = function(message, title = 'Info') {
    window.showToast({ type: 'info', title, message });
};

window.showLoading = function(message, title = 'Loading...') {
    return window.showToast({ type: 'loading', title, message, timeout: 0 });
};
</script>
