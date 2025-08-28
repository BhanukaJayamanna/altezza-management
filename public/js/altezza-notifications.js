/**
 * Altezza Real-time Notification System
 * Handles notification dropdown, real-time updates, and AJAX operations
 */

class AltezzaNotifications {
    constructor() {
        this.notificationDropdown = null;
        this.notificationBadge = null;
        this.notificationList = null;
        this.updateInterval = null;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        this.init();
    }

    init() {
        this.setupElements();
        this.loadNotifications();
        this.startPolling();
        this.setupEventListeners();
    }

    setupElements() {
        // These will be updated when we modify the sidebar template
        this.notificationBadge = document.querySelector('[data-notification-badge]');
        this.notificationList = document.querySelector('[data-notification-list]');
        this.markAllReadBtn = document.querySelector('[data-mark-all-read]');
    }

    setupEventListeners() {
        // Mark all as read
        if (this.markAllReadBtn) {
            this.markAllReadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.markAllAsRead();
            });
        }

        // Handle individual notification clicks
        if (this.notificationList) {
            this.notificationList.addEventListener('click', (e) => {
                const readBtn = e.target.closest('[data-mark-read]');
                const deleteBtn = e.target.closest('[data-delete-notification]');
                
                if (readBtn) {
                    e.preventDefault();
                    const notificationId = readBtn.dataset.notificationId;
                    this.markAsRead(notificationId);
                }
                
                if (deleteBtn) {
                    e.preventDefault();
                    const notificationId = deleteBtn.dataset.notificationId;
                    this.deleteNotification(notificationId);
                }
            });
        }
    }

    async loadNotifications() {
        try {
            const response = await fetch('/notifications', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateNotificationDisplay(data.notifications);
                this.updateBadge(data.unread_count);
            }
        } catch (error) {
            console.error('Failed to load notifications:', error);
        }
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(`/notifications/${notificationId}/read`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.ok) {
                this.loadNotifications(); // Reload to update UI
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('/notifications/mark-all-read', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.ok) {
                this.loadNotifications(); // Reload to update UI
                // Show toast notification
                if (window.AltezzaToast) {
                    window.AltezzaToast.success('All notifications marked as read');
                }
            }
        } catch (error) {
            console.error('Failed to mark all notifications as read:', error);
        }
    }

    async deleteNotification(notificationId) {
        try {
            const response = await fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.ok) {
                this.loadNotifications(); // Reload to update UI
            }
        } catch (error) {
            console.error('Failed to delete notification:', error);
        }
    }

    updateBadge(count) {
        if (this.notificationBadge) {
            if (count > 0) {
                this.notificationBadge.textContent = count > 99 ? '99+' : count;
                this.notificationBadge.style.display = 'flex';
            } else {
                this.notificationBadge.style.display = 'none';
            }
        }
    }

    updateNotificationDisplay(notifications) {
        if (!this.notificationList) return;

        if (notifications.length === 0) {
            this.notificationList.innerHTML = `
                <div class="px-4 py-8 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2z"></path>
                        </svg>
                        <p class="text-sm text-gray-500">No notifications yet</p>
                    </div>
                </div>
            `;
            return;
        }

        this.notificationList.innerHTML = notifications.map(notification => 
            this.renderNotification(notification)
        ).join('');
    }

    renderNotification(notification) {
        const iconMap = {
            'dollar': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>`,
            'warning': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>`,
            'check': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>`,
            'alert': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>`,
            'user': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>`,
            'info': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>`
        };

        const colorMap = {
            'blue': 'bg-blue-100 text-blue-600',
            'amber': 'bg-amber-100 text-amber-600',
            'green': 'bg-green-100 text-green-600',
            'red': 'bg-red-100 text-red-600',
            'purple': 'bg-purple-100 text-purple-600',
            'gray': 'bg-gray-100 text-gray-600'
        };

        const dotColorMap = {
            'blue': 'bg-blue-500',
            'amber': 'bg-amber-500',
            'green': 'bg-green-500',
            'red': 'bg-red-500',
            'purple': 'bg-purple-500',
            'gray': 'bg-gray-500'
        };

        const iconPath = iconMap[notification.icon] || iconMap['info'];
        const colorClass = colorMap[notification.color] || colorMap['blue'];
        const dotColor = dotColorMap[notification.color] || dotColorMap['blue'];

        const clickHandler = notification.action_url ? `onclick="window.location.href='${notification.action_url}'"` : '';

        return `
            <div class="px-4 py-3 hover:bg-slate-50 border-b border-slate-100 transition-colors duration-150 cursor-pointer" ${clickHandler}>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 ${colorClass} rounded-full flex items-center justify-center">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${iconPath}
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-slate-900">${notification.title}</p>
                            <div class="flex items-center space-x-2">
                                ${!notification.is_read ? `<div class="w-2 h-2 ${dotColor} rounded-full"></div>` : ''}
                                <span class="text-xs text-slate-500">${notification.time_ago}</span>
                                <button data-mark-read data-notification-id="${notification.id}" class="text-xs text-blue-600 hover:text-blue-800 ${notification.is_read ? 'hidden' : ''}">
                                    Mark read
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 mt-1">${notification.message}</p>
                    </div>
                </div>
            </div>
        `;
    }

    startPolling() {
        // Poll for new notifications every 2 minutes (120 seconds)
        this.updateInterval = setInterval(() => {
            this.loadNotifications();
        }, 30000);
    }

    stopPolling() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
            this.updateInterval = null;
        }
    }

    // Method to manually trigger notification refresh
    refresh() {
        this.loadNotifications();
    }
}

// Initialize notifications when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.AltezzaNotifications = new AltezzaNotifications();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AltezzaNotifications;
}
