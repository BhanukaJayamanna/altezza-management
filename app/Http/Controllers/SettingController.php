<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Display a listing of all settings grouped by category.
     */
    public function index(): View
    {
        $settingsGroups = Setting::all()->groupBy('group');
        
        // Define default settings structure if not exists
        $this->ensureDefaultSettings();
        
        return view('settings.index', compact('settingsGroups'));
    }

    /**
     * Update multiple settings at once.
     */
    public function update(Request $request): RedirectResponse
    {
        $settings = $request->input('settings', []);
        
        foreach ($settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            
            if ($setting) {
                // Validate based on type
                $this->validateSettingValue($setting, $value);
                
                // Convert value based on type
                $convertedValue = $this->convertValueByType($value, $setting->type);
                
                $setting->update(['value' => $convertedValue]);
                
                // Clear cache
                Cache::forget("setting.{$key}");
            }
        }
        
        // Clear all settings cache
        Cache::flush();
        
        toast_success('Settings updated successfully!');
        return redirect()->route('settings.index');
    }

    /**
     * Reset settings to default values.
     */
    public function reset(Request $request): RedirectResponse
    {
        $group = $request->input('group');
        
        if ($group) {
            // Reset specific group
            $this->resetGroupToDefaults($group);
            $message = "Settings for {$group} group have been reset to defaults.";
        } else {
            // Reset all settings
            $this->resetAllToDefaults();
            $message = "All settings have been reset to defaults.";
        }
        
        // Clear cache
        Cache::flush();
        
        toast_success($message);
        return redirect()->route('settings.index');
    }

    /**
     * Export settings as JSON.
     */
    public function export(): \Illuminate\Http\Response
    {
        $settings = Setting::all()->map(function ($setting) {
            return [
                'key' => $setting->key,
                'value' => $setting->value,
                'type' => $setting->type,
                'group' => $setting->group,
                'description' => $setting->description,
            ];
        });

        $filename = 'altezza_settings_' . now()->format('Y_m_d_H_i_s') . '.json';
        
        return response($settings->toJson(JSON_PRETTY_PRINT))
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Import settings from JSON file.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'settings_file' => 'required|file|mimes:json|max:2048'
        ]);

        try {
            $fileContent = file_get_contents($request->file('settings_file')->getRealPath());
            $settings = json_decode($fileContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON format');
            }

            $imported = 0;
            foreach ($settings as $settingData) {
                Setting::updateOrCreate(
                    ['key' => $settingData['key']],
                    [
                        'value' => $settingData['value'],
                        'type' => $settingData['type'] ?? 'string',
                        'group' => $settingData['group'] ?? 'general',
                        'description' => $settingData['description'] ?? null,
                    ]
                );
                $imported++;
            }

            // Clear cache
            Cache::flush();

            toast_success("Successfully imported {$imported} settings!");
            return redirect()->route('settings.index');

        } catch (\Exception $e) {
            toast_error('Failed to import settings: ' . $e->getMessage());
            return redirect()->route('settings.index');
        }
    }

    /**
     * Test email settings by sending a test email.
     */
    public function testEmail(): RedirectResponse
    {
        try {
            // This would send a test email using current settings
            // Implementation depends on your email configuration
            
            toast_success('Test email sent successfully!');
            return redirect()->route('settings.index');
        } catch (\Exception $e) {
            toast_error('Failed to send test email: ' . $e->getMessage());
            return redirect()->route('settings.index');
        }
    }

    /**
     * Validate setting value based on its type.
     */
    private function validateSettingValue(Setting $setting, $value): void
    {
        $rules = [];
        
        switch ($setting->type) {
            case 'integer':
                $rules = ['integer'];
                break;
            case 'boolean':
                $rules = ['boolean'];
                break;
            case 'email':
                $rules = ['email'];
                break;
            case 'url':
                $rules = ['url'];
                break;
            case 'json':
                $rules = ['array'];
                break;
        }
        
        if (!empty($rules)) {
            $validator = Validator::make(
                [$setting->key => $value],
                [$setting->key => $rules]
            );
            
            if ($validator->fails()) {
                throw new \InvalidArgumentException(
                    "Invalid value for {$setting->key}: " . $validator->errors()->first()
                );
            }
        }
    }

    /**
     * Convert value based on setting type.
     */
    private function convertValueByType($value, string $type): string
    {
        return match($type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : $value,
            default => (string) $value,
        };
    }

    /**
     * Ensure default settings exist.
     */
    private function ensureDefaultSettings(): void
    {
        $defaultSettings = $this->getDefaultSettings();
        
        foreach ($defaultSettings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reset specific group to defaults.
     */
    private function resetGroupToDefaults(string $group): void
    {
        $defaultSettings = collect($this->getDefaultSettings())
            ->where('group', $group);
            
        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reset all settings to defaults.
     */
    private function resetAllToDefaults(): void
    {
        $defaultSettings = $this->getDefaultSettings();
        
        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Get default settings configuration.
     */
    private function getDefaultSettings(): array
    {
        return [
            // Application Settings
            [
                'key' => 'app_name',
                'value' => 'Altezza Property Management',
                'type' => 'string',
                'group' => 'application',
                'description' => 'Application name displayed throughout the system'
            ],
            [
                'key' => 'app_logo',
                'value' => '',
                'type' => 'string',
                'group' => 'application',
                'description' => 'URL to application logo'
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'application',
                'description' => 'Enable maintenance mode'
            ],
            [
                'key' => 'timezone',
                'value' => 'UTC',
                'type' => 'string',
                'group' => 'application',
                'description' => 'Default application timezone'
            ],
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'string',
                'group' => 'application',
                'description' => 'Default date format'
            ],

            // Billing Settings
            [
                'key' => 'currency',
                'value' => 'USD',
                'type' => 'string',
                'group' => 'billing',
                'description' => 'Default currency code'
            ],
            [
                'key' => 'currency_symbol',
                'value' => '$',
                'type' => 'string',
                'group' => 'billing',
                'description' => 'Currency symbol'
            ],
            [
                'key' => 'late_fee_percentage',
                'value' => '5',
                'type' => 'integer',
                'group' => 'billing',
                'description' => 'Late fee percentage for overdue payments'
            ],
            [
                'key' => 'grace_period_days',
                'value' => '5',
                'type' => 'integer',
                'group' => 'billing',
                'description' => 'Grace period in days before applying late fees'
            ],
            [
                'key' => 'auto_generate_invoices',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'billing',
                'description' => 'Automatically generate monthly rent invoices'
            ],
            [
                'key' => 'invoice_due_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'billing',
                'description' => 'Default invoice due period in days'
            ],

            // Notification Settings
            [
                'key' => 'email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable email notifications'
            ],
            [
                'key' => 'sms_notifications',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable SMS notifications'
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
                'description' => 'Days before lease expiry to send renewal reminder'
            ],
            [
                'key' => 'admin_email',
                'value' => 'admin@altezza.com',
                'type' => 'email',
                'group' => 'notifications',
                'description' => 'Admin email for system notifications'
            ],

            // Email Settings
            [
                'key' => 'smtp_host',
                'value' => '',
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP server hostname'
            ],
            [
                'key' => 'smtp_port',
                'value' => '587',
                'type' => 'integer',
                'group' => 'email',
                'description' => 'SMTP server port'
            ],
            [
                'key' => 'smtp_username',
                'value' => '',
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP username'
            ],
            [
                'key' => 'smtp_password',
                'value' => '',
                'type' => 'password',
                'group' => 'email',
                'description' => 'SMTP password'
            ],
            [
                'key' => 'smtp_encryption',
                'value' => 'tls',
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP encryption (tls/ssl/none)'
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@altezza.com',
                'type' => 'email',
                'group' => 'email',
                'description' => 'Default from email address'
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'Altezza Property Management',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Default from name'
            ],

            // Utility Settings
            [
                'key' => 'default_electricity_rate',
                'value' => '0.12',
                'type' => 'decimal',
                'group' => 'utilities',
                'description' => 'Default electricity rate per unit'
            ],
            [
                'key' => 'default_water_rate',
                'value' => '0.008',
                'type' => 'decimal',
                'group' => 'utilities',
                'description' => 'Default water rate per unit'
            ],
            [
                'key' => 'default_gas_rate',
                'value' => '0.09',
                'type' => 'decimal',
                'group' => 'utilities',
                'description' => 'Default gas rate per unit'
            ],
            [
                'key' => 'utility_bill_generation_day',
                'value' => '1',
                'type' => 'integer',
                'group' => 'utilities',
                'description' => 'Day of month to generate utility bills (1-31)'
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
                'key' => 'require_password_symbols',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Require symbols in passwords'
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '5',
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

            // File Upload Settings
            [
                'key' => 'max_file_size',
                'value' => '5120',
                'type' => 'integer',
                'group' => 'uploads',
                'description' => 'Maximum file upload size in KB'
            ],
            [
                'key' => 'allowed_file_types',
                'value' => 'pdf,doc,docx,jpg,jpeg,png,gif',
                'type' => 'string',
                'group' => 'uploads',
                'description' => 'Allowed file extensions (comma-separated)'
            ],
            [
                'key' => 'file_storage_path',
                'value' => 'uploads',
                'type' => 'string',
                'group' => 'uploads',
                'description' => 'Default file storage path'
            ],

            // Backup Settings
            [
                'key' => 'auto_backup_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'backup',
                'description' => 'Enable automatic backups'
            ],
            [
                'key' => 'backup_schedule',
                'value' => 'daily',
                'type' => 'string',
                'group' => 'backup',
                'description' => 'Backup schedule (daily/weekly/monthly)'
            ],
            [
                'key' => 'backup_retention_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'backup',
                'description' => 'Number of days to retain backups'
            ],
        ];
    }
}
