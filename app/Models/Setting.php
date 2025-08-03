<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
    ];

    /**
     * Boot method to clear cache when settings are updated
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("setting.{$setting->key}");
            Cache::flush(); // Clear all cache instead of using tags
        });

        static::deleted(function ($setting) {
            Cache::forget("setting.{$setting->key}");
            Cache::flush(); // Clear all cache instead of using tags
        });
    }

    // Helper methods
    public static function getValue($key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return match($setting->type) {
                'integer' => (int) $setting->value,
                'decimal' => (float) $setting->value,
                'boolean' => (bool) $setting->value,
                'json' => json_decode($setting->value, true),
                default => $setting->value,
            };
        });
    }
    
    public static function setValue($key, $value, $type = 'string', $group = 'general', $description = null)
    {
        $value = match($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };
        
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );
    }

    /**
     * Get all settings as an array
     */
    public static function getAllSettings(): array
    {
        return Cache::remember('all_settings', 3600, function () {
            return static::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get settings by group
     */
    public static function getByGroup($group): array
    {
        return Cache::remember("settings_group_{$group}", 3600, function () use ($group) {
            return static::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Check if maintenance mode is enabled
     */
    public static function isMaintenanceMode(): bool
    {
        return (bool) static::getValue('maintenance_mode', false);
    }

    /**
     * Get application name
     */
    public static function getAppName(): string
    {
        return static::getValue('app_name', 'Altezza Property Management');
    }

    /**
     * Get currency settings
     */
    public static function getCurrencySettings(): array
    {
        return [
            'code' => static::getValue('currency', 'LKR'),
            'symbol' => static::getValue('currency_symbol', 'Rs.'),
        ];
    }

    /**
     * Get billing settings
     */
    public static function getBillingSettings(): array
    {
        return [
            'late_fee_percentage' => static::getValue('late_fee_percentage', 5),
            'grace_period_days' => static::getValue('grace_period_days', 5),
            'auto_generate_invoices' => static::getValue('auto_generate_invoices', true),
            'invoice_due_days' => static::getValue('invoice_due_days', 30),
        ];
    }

    /**
     * Get notification settings
     */
    public static function getNotificationSettings(): array
    {
        return [
            'email_enabled' => static::getValue('email_notifications', true),
            'sms_enabled' => static::getValue('sms_notifications', false),
            'payment_reminder_days' => explode(',', static::getValue('payment_reminder_days', '7,3,1')),
            'lease_renewal_reminder_days' => static::getValue('lease_renewal_reminder_days', 60),
            'admin_email' => static::getValue('admin_email', 'admin@altezza.com'),
        ];
    }
}
