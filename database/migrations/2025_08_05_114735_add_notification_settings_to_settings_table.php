<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert new notification settings
        DB::table('settings')->insert([
            [
                'key' => 'sms_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable SMS notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'twilio_account_sid',
                'value' => '',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'Twilio Account SID for SMS notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'twilio_auth_token',
                'value' => '',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'Twilio Auth Token for SMS notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'twilio_from_number',
                'value' => '',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'Twilio phone number for sending SMS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'push_notifications_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable browser push notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'vapid_public_key',
                'value' => '',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'VAPID public key for push notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'vapid_private_key',
                'value' => '',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'VAPID private key for push notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'notification_sound_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable notification sounds',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_sms_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Send SMS for maintenance updates',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'payment_sms_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Send SMS for payment reminders',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'lease_sms_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Send SMS for lease notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'emergency_sms_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Send SMS for emergency notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the notification settings
        DB::table('settings')->whereIn('key', [
            'sms_enabled',
            'twilio_account_sid',
            'twilio_auth_token',
            'twilio_from_number',
            'push_notifications_enabled',
            'vapid_public_key',
            'vapid_private_key',
            'notification_sound_enabled',
            'maintenance_sms_enabled',
            'payment_sms_enabled',
            'lease_sms_enabled',
            'emergency_sms_enabled',
        ])->delete();
    }
};
