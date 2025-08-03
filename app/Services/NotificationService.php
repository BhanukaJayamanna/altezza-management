<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create a payment notification
     */
    public function createPaymentNotification($userId, $amount, $apartmentNumber, $paymentId = null)
    {
        return Notification::createForUser(
            $userId,
            'payment',
            'Payment Received',
            "Payment of $" . number_format($amount, 2) . " received from Apartment {$apartmentNumber}",
            ['amount' => $amount, 'apartment_number' => $apartmentNumber, 'payment_id' => $paymentId],
            $paymentId ? route('payments.show', $paymentId) : null
        );
    }

    /**
     * Create a maintenance request notification
     */
    public function createMaintenanceNotification($userId, $title, $apartmentNumber, $requestId = null)
    {
        return Notification::createForUser(
            $userId,
            'maintenance',
            'Maintenance Request',
            "New maintenance request from Apartment {$apartmentNumber} - {$title}",
            ['apartment_number' => $apartmentNumber, 'request_id' => $requestId],
            $requestId ? route('maintenance-requests.show', $requestId) : null
        );
    }

    /**
     * Create a lease notification
     */
    public function createLeaseNotification($userId, $action, $apartmentNumber, $leaseId = null)
    {
        $actions = [
            'signed' => 'Lease Agreement Signed',
            'renewed' => 'Lease Agreement Renewed',
            'terminated' => 'Lease Agreement Terminated',
            'expired' => 'Lease Agreement Expired',
        ];

        $title = $actions[$action] ?? 'Lease Update';
        
        return Notification::createForUser(
            $userId,
            'lease',
            $title,
            "{$title} for Apartment {$apartmentNumber}",
            ['apartment_number' => $apartmentNumber, 'action' => $action, 'lease_id' => $leaseId],
            $leaseId ? route('leases.show', $leaseId) : null
        );
    }

    /**
     * Create an overdue payment notification
     */
    public function createOverdueNotification($userId, $apartmentNumber, $daysPastDue, $invoiceId = null)
    {
        return Notification::createForUser(
            $userId,
            'overdue',
            'Overdue Payment Alert',
            "Rent payment overdue for Apartment {$apartmentNumber} - {$daysPastDue} days late",
            ['apartment_number' => $apartmentNumber, 'days_past_due' => $daysPastDue, 'invoice_id' => $invoiceId],
            $invoiceId ? route('invoices.show', $invoiceId) : null
        );
    }

    /**
     * Create a user activity notification
     */
    public function createUserActivityNotification($userId, $activity, $details, $relatedId = null, $relatedRoute = null)
    {
        $activities = [
            'tenant_registered' => 'New Tenant Registration',
            'owner_added' => 'New Owner Added',
            'apartment_created' => 'New Apartment Created',
            'complaint_filed' => 'New Complaint Filed',
        ];

        $title = $activities[$activity] ?? 'User Activity';
        
        return Notification::createForUser(
            $userId,
            'user_activity',
            $title,
            $details,
            ['activity' => $activity, 'related_id' => $relatedId],
            $relatedRoute
        );
    }

    /**
     * Create system notification
     */
    public function createSystemNotification($userId, $title, $message, $data = null)
    {
        return Notification::createForUser(
            $userId,
            'system',
            $title,
            $message,
            $data
        );
    }

    /**
     * Notify all admins and managers
     */
    public function notifyAdminsAndManagers($callback)
    {
        $users = User::whereIn('role', ['admin', 'manager'])->get();
        
        foreach ($users as $user) {
            $callback($user->id);
        }
    }

    /**
     * Notify specific tenant
     */
    public function notifyTenant($tenantId, $callback)
    {
        $tenant = User::where('role', 'tenant')->find($tenantId);
        
        if ($tenant) {
            $callback($tenant->id);
        }
    }

    /**
     * Get notification statistics for user
     */
    public function getStats($userId): array
    {
        $total = Notification::where('user_id', $userId)->count();
        $unread = Notification::where('user_id', $userId)->unread()->count();
        $read = $total - $unread;

        $byType = Notification::where('user_id', $userId)
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return [
            'total' => $total,
            'unread' => $unread,
            'read' => $read,
            'by_type' => $byType,
        ];
    }

    /**
     * Clean old notifications (older than 30 days and read)
     */
    public function cleanOldNotifications(): int
    {
        return Notification::whereNotNull('read_at')
            ->where('created_at', '<', now()->subDays(30))
            ->delete();
    }
}
