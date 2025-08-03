/**
 * Advanced Toast Notification System for Altezza Property Management
 * 
 * Features:
 * - Multiple toast types (success, error, warning, info, loading)
 * - Auto-dismiss with progress bars
 * - Action buttons
 * - Promise-based loading toasts
 * - Batch operations
 * - Position control
 * - Sound notifications (optional)
 */

class AltezzaToast {
    constructor() {
        this.toasts = [];
        this.nextId = 1;
        this.position = 'top-right'; // top-right, top-left, bottom-right, bottom-left
        this.maxToasts = 5;
        this.soundEnabled = false;
        
        this.initializeContainer();
        this.bindEvents();
    }
    
    initializeContainer() {
        if (document.getElementById('altezza-toast-container')) return;
        
        const container = document.createElement('div');
        container.id = 'altezza-toast-container';
        container.className = `fixed z-50 space-y-2 max-w-sm w-full ${this.getPositionClasses()}`;
        document.body.appendChild(container);
    }
    
    getPositionClasses() {
        const positions = {
            'top-right': 'top-4 right-4',
            'top-left': 'top-4 left-4',
            'bottom-right': 'bottom-4 right-4',
            'bottom-left': 'bottom-4 left-4',
            'top-center': 'top-4 left-1/2 transform -translate-x-1/2',
            'bottom-center': 'bottom-4 left-1/2 transform -translate-x-1/2'
        };
        return positions[this.position] || positions['top-right'];
    }
    
    bindEvents() {
        // Listen for custom toast events
        window.addEventListener('altezza-toast', (e) => {
            this.show(e.detail);
        });
        
        // Listen for Laravel flash messages
        this.processServerMessages();
    }
    
    processServerMessages() {
        // This will be called after DOM loads to show server-side messages
        const messages = window.altezzaServerMessages || {};
        
        Object.entries(messages).forEach(([type, message], index) => {
            setTimeout(() => {
                this.show({
                    type,
                    title: this.getDefaultTitle(type),
                    message,
                    timeout: type === 'error' ? 7000 : 5000
                });
            }, index * 100);
        });
    }
    
    getDefaultTitle(type) {
        const titles = {
            success: 'Success!',
            error: 'Error!',
            warning: 'Warning!',
            info: 'Info',
            loading: 'Loading...'
        };
        return titles[type] || 'Notification';
    }
    
    show(options) {
        const toast = {
            id: this.nextId++,
            type: options.type || 'info',
            title: options.title || this.getDefaultTitle(options.type),
            message: options.message || '',
            action: options.action || null,
            timeout: options.timeout !== undefined ? options.timeout : 
                    (options.type === 'error' ? 0 : 5000),
            persistent: options.persistent || false,
            progress: 100,
            interval: 100,
            onShow: options.onShow || null,
            onHide: options.onHide || null,
            onAction: options.onAction || null
        };
        
        this.toasts.push(toast);
        this.renderToast(toast);
        
        // Limit toasts
        if (this.toasts.length > this.maxToasts) {
            this.hide(this.toasts[0].id);
        }
        
        // Play sound if enabled
        if (this.soundEnabled) {
            this.playSound(toast.type);
        }
        
        // Start countdown
        if (toast.timeout > 0 && !toast.persistent) {
            this.startCountdown(toast);
        }
        
        // Callback
        if (toast.onShow) toast.onShow(toast);
        
        return toast.id;
    }
    
    renderToast(toast) {
        const container = document.getElementById('altezza-toast-container');
        const toastEl = document.createElement('div');
        toastEl.id = `toast-${toast.id}`;
        toastEl.className = `transform transition-all duration-300 ease-out opacity-0 translate-x-full`;
        
        toastEl.innerHTML = `
            <div class="relative flex items-center p-4 rounded-lg shadow-lg text-white min-h-[60px] ${this.getToastColor(toast.type)}">
                <!-- Icon -->
                <div class="flex-shrink-0 mr-3">
                    ${this.getIcon(toast.type)}
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-sm">${toast.title}</div>
                    ${toast.message ? `<div class="text-sm opacity-90 mt-1">${toast.message}</div>` : ''}
                </div>
                
                <!-- Action Button -->
                ${toast.action ? `
                    <div class="flex-shrink-0 ml-3">
                        <button onclick="altezzaToast.handleAction('${toast.id}')"
                                class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-1 rounded text-xs font-medium transition-colors duration-200">
                            ${toast.action.label}
                        </button>
                    </div>
                ` : ''}
                
                <!-- Close Button -->
                <button onclick="altezzaToast.hide('${toast.id}')" 
                        class="flex-shrink-0 ml-3 p-1 rounded-full hover:bg-white hover:bg-opacity-20 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <!-- Progress Bar -->
                ${toast.timeout > 0 && !toast.persistent ? `
                    <div id="progress-${toast.id}" 
                         class="absolute bottom-0 left-0 h-1 bg-white bg-opacity-30 rounded-bl-lg rounded-br-lg"
                         style="width: 100%; transition: width ${toast.interval}ms linear;">
                    </div>
                ` : ''}
            </div>
        `;
        
        container.appendChild(toastEl);
        
        // Trigger animation
        setTimeout(() => {
            toastEl.className = `transform transition-all duration-300 ease-out opacity-100 translate-x-0`;
        }, 10);
    }
    
    getToastColor(type) {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500',
            loading: 'bg-gray-500'
        };
        return colors[type] || colors.info;
    }
    
    getIcon(type) {
        const icons = {
            success: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>`,
            error: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                   </svg>`,
            warning: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                     </svg>`,
            info: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>`,
            loading: `<svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                       <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                       <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                     </svg>`
        };
        return icons[type] || icons.info;
    }
    
    hide(id) {
        const toast = this.toasts.find(t => t.id == id);
        if (!toast) return;
        
        const toastEl = document.getElementById(`toast-${id}`);
        if (toastEl) {
            toastEl.className = `transform transition-all duration-200 ease-in opacity-0 translate-x-full`;
            
            setTimeout(() => {
                toastEl.remove();
                this.toasts = this.toasts.filter(t => t.id != id);
                
                if (toast.onHide) toast.onHide(toast);
            }, 200);
        }
    }
    
    handleAction(id) {
        const toast = this.toasts.find(t => t.id == id);
        if (!toast || !toast.action) return;
        
        if (toast.action.callback) {
            toast.action.callback();
        } else if (toast.action.url) {
            window.location.href = toast.action.url;
        }
        
        if (toast.onAction) toast.onAction(toast);
        
        this.hide(id);
    }
    
    startCountdown(toast) {
        const totalTime = toast.timeout;
        let elapsedTime = 0;
        
        const timer = setInterval(() => {
            elapsedTime += toast.interval;
            toast.progress = Math.max(0, ((totalTime - elapsedTime) / totalTime) * 100);
            
            const progressEl = document.getElementById(`progress-${toast.id}`);
            if (progressEl) {
                progressEl.style.width = `${toast.progress}%`;
            }
            
            if (elapsedTime >= totalTime) {
                clearInterval(timer);
                this.hide(toast.id);
            }
        }, toast.interval);
        
        toast.timer = timer;
    }
    
    playSound(type) {
        // Optional sound notifications
        if (typeof Audio !== 'undefined') {
            const sounds = {
                success: '/sounds/success.mp3',
                error: '/sounds/error.mp3',
                warning: '/sounds/warning.mp3',
                info: '/sounds/info.mp3'
            };
            
            if (sounds[type]) {
                const audio = new Audio(sounds[type]);
                audio.volume = 0.3;
                audio.play().catch(() => {}); // Ignore errors
            }
        }
    }
    
    // Convenience methods
    success(message, title, options = {}) {
        return this.show({ type: 'success', title, message, ...options });
    }
    
    error(message, title, options = {}) {
        return this.show({ type: 'error', title, message, timeout: 0, ...options });
    }
    
    warning(message, title, options = {}) {
        return this.show({ type: 'warning', title, message, ...options });
    }
    
    info(message, title, options = {}) {
        return this.show({ type: 'info', title, message, ...options });
    }
    
    loading(message, title = 'Loading...', options = {}) {
        return this.show({ type: 'loading', title, message, timeout: 0, persistent: true, ...options });
    }
    
    // Promise-based loading toast
    async loadingPromise(promise, loadingMessage = 'Processing...', successMessage = 'Completed!', errorMessage = 'Failed!') {
        const loadingId = this.loading(loadingMessage);
        
        try {
            const result = await promise;
            this.hide(loadingId);
            this.success(successMessage);
            return result;
        } catch (error) {
            this.hide(loadingId);
            this.error(errorMessage || error.message);
            throw error;
        }
    }
    
    // Batch operations
    showBatch(toasts, delay = 100) {
        toasts.forEach((toast, index) => {
            setTimeout(() => this.show(toast), index * delay);
        });
    }
    
    // Clear all toasts
    clear() {
        this.toasts.forEach(toast => {
            if (toast.timer) clearInterval(toast.timer);
            this.hide(toast.id);
        });
    }
    
    // Configuration
    setPosition(position) {
        this.position = position;
        const container = document.getElementById('altezza-toast-container');
        if (container) {
            container.className = `fixed z-50 space-y-2 max-w-sm w-full ${this.getPositionClasses()}`;
        }
    }
    
    setMaxToasts(max) {
        this.maxToasts = max;
    }
    
    enableSound(enabled = true) {
        this.soundEnabled = enabled;
    }
}

// Initialize global instance
window.altezzaToast = new AltezzaToast();

// Global convenience functions for backward compatibility
window.showToast = (options) => window.altezzaToast.show(options);
window.showSuccess = (message, title) => window.altezzaToast.success(message, title);
window.showError = (message, title) => window.altezzaToast.error(message, title);
window.showWarning = (message, title) => window.altezzaToast.warning(message, title);
window.showInfo = (message, title) => window.altezzaToast.info(message, title);
window.showLoading = (message, title) => window.altezzaToast.loading(message, title);

// jQuery integration (if available)
if (typeof $ !== 'undefined') {
    $.extend({
        toast: window.altezzaToast,
        showToast: window.showToast,
        showSuccess: window.showSuccess,
        showError: window.showError,
        showWarning: window.showWarning,
        showInfo: window.showInfo,
        showLoading: window.showLoading
    });
}
