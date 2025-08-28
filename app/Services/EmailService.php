<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\User;
use App\Models\Lease;
use App\Mail\InvoiceGenerated;
use App\Mail\PaymentReminder;
use App\Mail\WelcomeTenant;
use App\Mail\LeaseExpiryNotice;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send invoice generated notification
     */
    public function sendInvoiceGenerated(Invoice $invoice): bool
    {
        try {
            $tenant = $invoice->tenant;
            if (!$tenant || !$tenant->email) {
                Log::warning("Cannot send invoice email - tenant or email not found for invoice {$invoice->id}");
                return false;
            }

            Mail::to($tenant->email)->send(new InvoiceGenerated($invoice));
            
            Log::info("Invoice generated email sent to {$tenant->email} for invoice {$invoice->invoice_number}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send invoice generated email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send payment reminder
     */
    public function sendPaymentReminder(Invoice $invoice): bool
    {
        try {
            $tenant = $invoice->tenant;
            if (!$tenant || !$tenant->email) {
                Log::warning("Cannot send payment reminder - tenant or email not found for invoice {$invoice->id}");
                return false;
            }

            Mail::to($tenant->email)->send(new PaymentReminder($invoice));
            
            Log::info("Payment reminder sent to {$tenant->email} for invoice {$invoice->invoice_number}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send payment reminder: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send welcome email to new tenant
     */
    public function sendWelcomeTenant(User $tenant, Lease $lease): bool
    {
        try {
            if (!$tenant->email) {
                Log::warning("Cannot send welcome email - tenant email not found for user {$tenant->id}");
                return false;
            }

            Mail::to($tenant->email)->send(new WelcomeTenant($tenant, $lease));
            
            Log::info("Welcome email sent to new tenant: {$tenant->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send welcome tenant email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send lease expiry notice
     */
    public function sendLeaseExpiryNotice(Lease $lease): bool
    {
        try {
            $tenant = $lease->tenant;
            if (!$tenant || !$tenant->email) {
                Log::warning("Cannot send lease expiry notice - tenant or email not found for lease {$lease->id}");
                return false;
            }

            Mail::to($tenant->email)->send(new LeaseExpiryNotice($lease));
            
            Log::info("Lease expiry notice sent to {$tenant->email} for lease ending {$lease->end_date}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send lease expiry notice: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk payment reminders for overdue invoices
     */
    public function sendBulkPaymentReminders(): int
    {
        $overdueInvoices = Invoice::where('status', 'pending')
            ->where('due_date', '<', now())
            ->with('tenant')
            ->get();

        $sentCount = 0;
        foreach ($overdueInvoices as $invoice) {
            if ($this->sendPaymentReminder($invoice)) {
                $sentCount++;
            }
        }

        Log::info("Bulk payment reminders sent: {$sentCount} out of {$overdueInvoices->count()} overdue invoices");
        return $sentCount;
    }

    /**
     * Send lease expiry notifications for leases expiring in 30 days
     */
    public function sendLeaseExpiryNotifications(): int
    {
        $expiringLeases = Lease::where('status', 'active')
            ->where('end_date', '<=', now()->addDays(30))
            ->where('end_date', '>', now())
            ->with('tenant')
            ->get();

        $sentCount = 0;
        foreach ($expiringLeases as $lease) {
            if ($this->sendLeaseExpiryNotice($lease)) {
                $sentCount++;
            }
        }

        Log::info("Lease expiry notifications sent: {$sentCount} out of {$expiringLeases->count()} expiring leases");
        return $sentCount;
    }
}
