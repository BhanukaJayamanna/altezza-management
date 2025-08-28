<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SmsService
{
    protected $client;
    protected $fromNumber;
    protected $enabled;

    public function __construct()
    {
        try {
            $this->enabled = Setting::getValue('sms_enabled', false);
            
            if ($this->enabled) {
                $accountSid = Setting::getValue('twilio_account_sid');
                $authToken = Setting::getValue('twilio_auth_token');
                $this->fromNumber = Setting::getValue('twilio_from_number');
                
                if ($accountSid && $authToken) {
                    $this->client = new Client($accountSid, $authToken);
                }
            }
        } catch (\Exception $e) {
            // Settings table might not have SMS settings yet
            $this->enabled = false;
            Log::info('SMS settings not found, SMS disabled', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send SMS notification
     */
    public function send($to, $message)
    {
        if (!$this->enabled || !$this->client) {
            Log::info('SMS not enabled or not configured', [
                'to' => $to,
                'message' => $message
            ]);
            return false;
        }

        try {
            $result = $this->client->messages->create($to, [
                'from' => $this->fromNumber,
                'body' => $message
            ]);

            Log::info('SMS sent successfully', [
                'to' => $to,
                'message_sid' => $result->sid
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to send SMS', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send payment reminder SMS
     */
    public function sendPaymentReminder($tenant, $amount, $dueDate)
    {
        if (!$tenant->phone) {
            return false;
        }

        $message = "Hi {$tenant->first_name}, your rent payment of ${amount} is due on {$dueDate}. Please make payment to avoid late fees. - Altezza Property Management";
        
        return $this->send($tenant->phone, $message);
    }

    /**
     * Send maintenance update SMS
     */
    public function sendMaintenanceUpdate($tenant, $requestId, $status)
    {
        if (!$tenant->phone) {
            return false;
        }

        $message = "Maintenance request #{$requestId} status updated to: {$status}. Check your tenant portal for details. - Altezza Property Management";
        
        return $this->send($tenant->phone, $message);
    }

    /**
     * Send lease expiry reminder SMS
     */
    public function sendLeaseExpiryReminder($tenant, $expiryDate, $daysLeft)
    {
        if (!$tenant->phone) {
            return false;
        }

        $message = "Your lease expires in {$daysLeft} days on {$expiryDate}. Please contact us to discuss renewal options. - Altezza Property Management";
        
        return $this->send($tenant->phone, $message);
    }

    /**
     * Send emergency notification SMS
     */
    public function sendEmergencyNotification($tenant, $message)
    {
        if (!$tenant->phone) {
            return false;
        }

        $fullMessage = "URGENT: {$message} - Altezza Property Management";
        
        return $this->send($tenant->phone, $fullMessage);
    }

    /**
     * Test SMS functionality
     */
    public function sendTestSms($to)
    {
        $message = "Test SMS from Altezza Property Management System. SMS notifications are working correctly.";
        return $this->send($to, $message);
    }

    /**
     * Check if SMS is enabled and configured
     */
    public function isConfigured()
    {
        return $this->enabled && $this->client && $this->fromNumber;
    }
}
