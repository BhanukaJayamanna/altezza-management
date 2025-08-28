/**
 * Altezza Push Notification Manager
 * Handles browser push notification setup and management
 */

class AltezzaPushNotifications {
    constructor() {
        this.isSupported = 'serviceWorker' in navigator && 'PushManager' in window;
        this.isSubscribed = false;
        this.swRegistration = null;
        this.applicationServerKey = null; // Will be set from backend
        
        this.init();
    }

    async init() {
        if (!this.isSupported) {
            console.warn('Push notifications are not supported in this browser');
            return;
        }

        try {
            await this.registerServiceWorker();
            await this.checkSubscription();
            this.setupUI();
        } catch (error) {
            console.error('Failed to initialize push notifications:', error);
        }
    }

    async registerServiceWorker() {
        this.swRegistration = await navigator.serviceWorker.register('/sw.js');
        console.log('Service Worker registered:', this.swRegistration);
    }

    async checkSubscription() {
        const subscription = await this.swRegistration.pushManager.getSubscription();
        this.isSubscribed = !(subscription === null);
        
        if (this.isSubscribed) {
            console.log('User is subscribed to push notifications');
        } else {
            console.log('User is NOT subscribed to push notifications');
        }
    }

    async requestPermission() {
        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            console.log('Notification permission granted');
            return true;
        } else if (permission === 'denied') {
            console.log('Notification permission denied');
            return false;
        } else {
            console.log('Notification permission dismissed');
            return false;
        }
    }

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    async subscribeUser() {
        try {
            // Request permission first
            const hasPermission = await this.requestPermission();
            if (!hasPermission) {
                throw new Error('Permission not granted');
            }

            // Get application server key from backend
            const response = await fetch('/api/vapid-public-key');
            const { publicKey } = await response.json();
            this.applicationServerKey = this.urlBase64ToUint8Array(publicKey);

            // Subscribe to push notifications
            const subscription = await this.swRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.applicationServerKey
            });

            console.log('User subscribed:', subscription);

            // Send subscription to backend
            await this.sendSubscriptionToBackend(subscription);
            
            this.isSubscribed = true;
            this.updateUI();
            
            return subscription;
        } catch (error) {
            console.error('Failed to subscribe user:', error);
            throw error;
        }
    }

    async unsubscribeUser() {
        try {
            const subscription = await this.swRegistration.pushManager.getSubscription();
            
            if (subscription) {
                await subscription.unsubscribe();
                await this.removeSubscriptionFromBackend(subscription);
                console.log('User unsubscribed');
            }
            
            this.isSubscribed = false;
            this.updateUI();
        } catch (error) {
            console.error('Failed to unsubscribe user:', error);
            throw error;
        }
    }

    async sendSubscriptionToBackend(subscription) {
        const response = await fetch('/api/push-subscriptions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                subscription: subscription.toJSON()
            })
        });

        if (!response.ok) {
            throw new Error('Failed to save subscription');
        }
    }

    async removeSubscriptionFromBackend(subscription) {
        const response = await fetch('/api/push-subscriptions', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                subscription: subscription.toJSON()
            })
        });

        if (!response.ok) {
            throw new Error('Failed to remove subscription');
        }
    }

    setupUI() {
        // Create notification settings button if it doesn't exist
        const existingBtn = document.getElementById('push-notification-toggle');
        if (!existingBtn) {
            this.createNotificationToggle();
        }
        this.updateUI();
    }

    createNotificationToggle() {
        // This would be integrated into the settings page
        const container = document.querySelector('.notification-settings');
        if (!container) return;

        const toggle = document.createElement('div');
        toggle.innerHTML = `
            <div class="flex items-center justify-between py-3">
                <div>
                    <label class="text-sm font-medium text-gray-700">Push Notifications</label>
                    <p class="text-xs text-gray-500">Receive notifications even when the app is closed</p>
                </div>
                <button id="push-notification-toggle" 
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        role="switch">
                    <span class="sr-only">Enable push notifications</span>
                    <span id="push-toggle-indicator" 
                          class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform">
                    </span>
                </button>
            </div>
        `;
        
        container.appendChild(toggle);

        // Add event listener
        document.getElementById('push-notification-toggle').addEventListener('click', () => {
            if (this.isSubscribed) {
                this.unsubscribeUser();
            } else {
                this.subscribeUser();
            }
        });
    }

    updateUI() {
        const toggleBtn = document.getElementById('push-notification-toggle');
        const indicator = document.getElementById('push-toggle-indicator');
        
        if (toggleBtn && indicator) {
            if (this.isSubscribed) {
                toggleBtn.classList.add('bg-blue-600');
                toggleBtn.classList.remove('bg-gray-200');
                indicator.classList.add('translate-x-5');
                indicator.classList.remove('translate-x-1');
            } else {
                toggleBtn.classList.add('bg-gray-200');
                toggleBtn.classList.remove('bg-blue-600');
                indicator.classList.add('translate-x-1');
                indicator.classList.remove('translate-x-5');
            }
        }
    }

    // Test push notification
    async sendTestNotification() {
        try {
            await fetch('/api/test-push-notification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            console.log('Test notification sent');
        } catch (error) {
            console.error('Failed to send test notification:', error);
        }
    }
}

// Initialize push notifications
let altezzaPushNotifications;

document.addEventListener('DOMContentLoaded', function() {
    altezzaPushNotifications = new AltezzaPushNotifications();
    
    // Make globally available
    window.altezzaPushNotifications = altezzaPushNotifications;
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AltezzaPushNotifications;
}
