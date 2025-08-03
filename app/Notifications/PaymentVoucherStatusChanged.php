<?php

namespace App\Notifications;

use App\Models\PaymentVoucher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVoucherStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $voucher;
    protected $action;

    /**
     * Create a new notification instance.
     */
    public function __construct(PaymentVoucher $voucher, string $action)
    {
        $this->voucher = $voucher;
        $this->action = $action; // 'approved' or 'rejected'
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Payment Voucher ' . ucfirst($this->action);
        $greeting = $this->action === 'approved' ? 'Great news!' : 'Update on your voucher';
        
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line("Your payment voucher {$this->voucher->voucher_number} has been {$this->action}.")
            ->line("**Voucher Details:**")
            ->line("- Voucher Number: {$this->voucher->voucher_number}")
            ->line("- Vendor: {$this->voucher->vendor_name}")
            ->line("- Amount: ₹" . number_format($this->voucher->amount, 2))
            ->line("- Description: {$this->voucher->description}");

        if ($this->voucher->apartment) {
            $message->line("- Property: {$this->voucher->apartment->unit_number}");
        }

        if ($this->action === 'approved') {
            $message->line('The payment will be processed shortly.')
                   ->action('View Voucher', route('vouchers.show', $this->voucher));
        } else {
            $message->line("Reason: {$this->voucher->rejection_reason}")
                   ->action('View Voucher', route('vouchers.show', $this->voucher));
        }

        return $message->line('Thank you for using our payment system!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'voucher_id' => $this->voucher->id,
            'voucher_number' => $this->voucher->voucher_number,
            'vendor_name' => $this->voucher->vendor_name,
            'amount' => $this->voucher->amount,
            'action' => $this->action,
            'message' => "Payment voucher {$this->voucher->voucher_number} has been {$this->action}",
        ];
    }
}