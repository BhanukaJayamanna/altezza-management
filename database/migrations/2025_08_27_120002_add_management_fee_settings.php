<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add management fee settings
        $managementFeeSettings = [
            [
                'key' => 'management_fee_ratio',
                'value' => '14.00',
                'type' => 'decimal',
                'group' => 'management_fees',
                'description' => 'Current ratio for calculating management fees (per square foot)'
            ],
            [
                'key' => 'sinking_fund_ratio',
                'value' => '2.50',
                'type' => 'decimal',
                'group' => 'management_fees',
                'description' => 'Current ratio for calculating sinking fund (per square foot)'
            ],
            [
                'key' => 'management_fee_billing_cycle',
                'value' => 'quarterly',
                'type' => 'string',
                'group' => 'management_fees',
                'description' => 'Billing cycle for management fees (quarterly, monthly, annually)'
            ],
            [
                'key' => 'management_fee_due_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'management_fees',
                'description' => 'Number of days after invoice generation when payment is due'
            ],
            [
                'key' => 'management_fee_late_fee_percentage',
                'value' => '5.00',
                'type' => 'decimal',
                'group' => 'management_fees',
                'description' => 'Late fee percentage for overdue management fee payments'
            ],
            [
                'key' => 'management_fee_auto_generate',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'management_fees',
                'description' => 'Automatically generate management fee invoices quarterly'
            ],
            [
                'key' => 'management_fee_currency_symbol',
                'value' => 'LKR ',
                'type' => 'string',
                'group' => 'management_fees',
                'description' => 'Currency symbol for management fee displays'
            ]
        ];

        foreach ($managementFeeSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'management_fee_ratio',
            'sinking_fund_ratio',
            'management_fee_billing_cycle',
            'management_fee_due_days',
            'management_fee_late_fee_percentage',
            'management_fee_auto_generate',
            'management_fee_currency_symbol'
        ];

        Setting::whereIn('key', $keys)->delete();
    }
};
