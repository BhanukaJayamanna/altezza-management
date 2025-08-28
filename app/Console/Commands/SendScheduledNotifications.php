<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Setting;
use App\Services\NotificationService;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:send-scheduled';

    /**
     * The console command description.
     */
    protected $description = 'Send scheduled notifications for payments and maintenance';

    protected $notificationService;
    protected $smsService;

    public function __construct(NotificationService $notificationService, SmsService $smsService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
        $this->smsService = $smsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting scheduled notifications...');

        try {
            $this->sendPaymentReminders();
            $this->sendOverdueNotifications();
            
            $this->info('Scheduled notifications completed successfully.');
        } catch (\Exception $e) {
            $this->error('Error sending scheduled notifications: ' . $e->getMessage());
            Log::error('Scheduled notifications failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send payment reminder notifications
     */
    protected function sendPaymentReminders()
    {
        $reminderDays = explode(',', Setting::getValue('payment_reminder_days', '7,3,1'));
        $today = Carbon::today();

        foreach ($reminderDays as $days) {
            $targetDate = $today->copy()->addDays((int)$days);
            
            $upcomingInvoices = Invoice::where('due_date', $targetDate->format('Y-m-d'))
                ->where('status', 'pending')
                ->with(['apartment', 'tenant'])
                ->get();

            foreach ($upcomingInvoices as $invoice) {
                if ($invoice->tenant && $invoice->apartment) {
                    $owner = $invoice->tenant;
                    $apartment = $invoice->apartment;

                    // Create database notification
                    $this->notificationService->createPaymentReminderNotification(
                        $owner->id,
                        $invoice->amount,
                        $apartment->apartment_number,
                        $invoice->due_date,
                        $invoice->id
                    );

                    // Send SMS if enabled
                    if (Setting::getValue('sms_enabled', false)) {
                        $this->smsService->sendPaymentReminder(
                            $owner,
                            $invoice->amount,
                            $targetDate->format('M j, Y')
                        );
                    }

                    $this->info("Payment reminder sent to {$owner->name} - Apartment {$apartment->apartment_number}");
                }
            }
        }
    }

    /**
     * Send overdue payment notifications
     */
    protected function sendOverdueNotifications()
    {
        $today = Carbon::today();
        
        $overdueInvoices = Invoice::where('due_date', '<', $today->format('Y-m-d'))
            ->where('status', 'pending')
            ->with(['apartment', 'tenant'])
            ->get();

        foreach ($overdueInvoices as $invoice) {
            if ($invoice->tenant && $invoice->apartment) {
                $owner = $invoice->tenant;
                $apartment = $invoice->apartment;
                $daysOverdue = $today->diffInDays(Carbon::parse($invoice->due_date));

                // Create database notification
                $this->notificationService->createOverdueNotification(
                    $owner->id,
                    $invoice->amount,
                    $apartment->apartment_number,
                    $daysOverdue,
                    $invoice->id
                );

                $this->info("Overdue notice sent to {$owner->name} - Apartment {$apartment->apartment_number} ({$daysOverdue} days overdue)");
            }
        }
    }
}
