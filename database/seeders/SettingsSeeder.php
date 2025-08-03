<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'Altezza Property Management',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Application name displayed throughout the system'
            ],
            [
                'key' => 'app_logo',
                'value' => '',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Path to application logo'
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Colombo',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default application timezone'
            ],
            [
                'key' => 'date_format',
                'value' => 'd/m/Y',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Date format used throughout the application'
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'general',
                'description' => 'Enable maintenance mode to restrict access'
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'System is under maintenance. Please try again later.',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Message shown during maintenance mode'
            ],

            // Financial Settings
            [
                'key' => 'currency',
                'value' => 'LKR',
                'type' => 'string',
                'group' => 'financial',
                'description' => 'Default currency code'
            ],
            [
                'key' => 'currency_symbol',
                'value' => 'Rs.',
                'type' => 'string',
                'group' => 'financial',
                'description' => 'Currency symbol'
            ],
            [
                'key' => 'late_fee_percentage',
                'value' => '5',
                'type' => 'decimal',
                'group' => 'financial',
                'description' => 'Late payment fee percentage'
            ],
            [
                'key' => 'grace_period_days',
                'value' => '5',
                'type' => 'integer',
                'group' => 'financial',
                'description' => 'Grace period days before late fees apply'
            ],
            [
                'key' => 'invoice_due_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'financial',
                'description' => 'Default invoice due days'
            ],
            [
                'key' => 'auto_generate_invoices',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'financial',
                'description' => 'Automatically generate monthly invoices'
            ],

            // Email Settings
            [
                'key' => 'email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable email notifications'
            ],
            [
                'key' => 'admin_email',
                'value' => 'admin@altezza.com',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'Administrator email address'
            ],
            [
                'key' => 'from_email',
                'value' => 'noreply@altezza.com',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'From email address for system emails'
            ],
            [
                'key' => 'from_name',
                'value' => 'Altezza Property Management',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'From name for system emails'
            ],
            [
                'key' => 'payment_reminder_days',
                'value' => '7,3,1',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'Days before due date to send payment reminders (comma-separated)'
            ],
            [
                'key' => 'lease_renewal_reminder_days',
                'value' => '60',
                'type' => 'integer',
                'group' => 'notifications',
                'description' => 'Days before lease expiry to send renewal reminders'
            ],

            // SMS Settings
            [
                'key' => 'sms_notifications',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable SMS notifications'
            ],
            [
                'key' => 'sms_provider',
                'value' => 'twilio',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'SMS service provider'
            ],
            [
                'key' => 'twilio_sid',
                'value' => '',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'Twilio Account SID'
            ],
            [
                'key' => 'twilio_token',
                'value' => '',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'Twilio Auth Token'
            ],
            [
                'key' => 'twilio_from',
                'value' => '',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'Twilio From Number'
            ],

            // Utility Settings
            [
                'key' => 'electricity_unit_price',
                'value' => '0.12',
                'type' => 'decimal',
                'group' => 'utilities',
                'description' => 'Price per unit of electricity'
            ],
            [
                'key' => 'water_unit_price',
                'value' => '0.05',
                'type' => 'decimal',
                'group' => 'utilities',
                'description' => 'Price per unit of water'
            ],
            [
                'key' => 'gas_unit_price',
                'value' => '0.08',
                'type' => 'decimal',
                'group' => 'utilities',
                'description' => 'Price per unit of gas'
            ],
            [
                'key' => 'auto_generate_utility_bills',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'utilities',
                'description' => 'Automatically generate utility bills from meter readings'
            ],

            // Maintenance Settings
            [
                'key' => 'auto_assign_maintenance',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'maintenance',
                'description' => 'Automatically assign maintenance requests'
            ],
            [
                'key' => 'maintenance_sla_hours',
                'value' => '24',
                'type' => 'integer',
                'group' => 'maintenance',
                'description' => 'Standard response time for maintenance requests (hours)'
            ],
            [
                'key' => 'urgent_maintenance_sla_hours',
                'value' => '4',
                'type' => 'integer',
                'group' => 'maintenance',
                'description' => 'Response time for urgent maintenance requests (hours)'
            ],

            // Security Settings
            [
                'key' => 'session_timeout',
                'value' => '120',
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Session timeout in minutes'
            ],
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Minimum password length'
            ],
            [
                'key' => 'require_password_complexity',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Require complex passwords'
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '3',
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Maximum login attempts before lockout'
            ],
            [
                'key' => 'lockout_duration',
                'value' => '15',
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Account lockout duration in minutes'
            ],

            // Backup Settings
            [
                'key' => 'auto_backup',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'backup',
                'description' => 'Enable automatic backups'
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'type' => 'string',
                'group' => 'backup',
                'description' => 'Backup frequency (daily, weekly, monthly)'
            ],
            [
                'key' => 'backup_retention_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'backup',
                'description' => 'Number of days to retain backups'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
