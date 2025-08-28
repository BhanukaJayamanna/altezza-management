<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            [
                'key' => 'rooftop_base_rate',
                'value' => '500.00',
                'type' => 'decimal',
                'description' => 'Base rate for rooftop reservations',
                'group' => 'rooftop',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'rooftop_hourly_rate',
                'value' => '100.00',
                'type' => 'decimal',
                'description' => 'Hourly rate for rooftop reservations',
                'group' => 'rooftop',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'rooftop_cleaning_fee',
                'value' => '150.00',
                'type' => 'decimal',
                'description' => 'Cleaning fee for rooftop reservations',
                'group' => 'rooftop',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'rooftop_security_deposit',
                'value' => '1000.00',
                'type' => 'decimal',
                'description' => 'Security deposit for rooftop reservations',
                'group' => 'rooftop',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'rooftop_max_guests',
                'value' => '200',
                'type' => 'integer',
                'description' => 'Maximum number of guests allowed for rooftop reservations',
                'group' => 'rooftop',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'rooftop_advance_booking_days',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Maximum days in advance for rooftop reservations',
                'group' => 'rooftop',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'rooftop_operating_hours_start',
                'value' => '08:00',
                'type' => 'string',
                'description' => 'Rooftop operating hours start time',
                'group' => 'rooftop',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'rooftop_operating_hours_end',
                'value' => '23:00',
                'type' => 'string',
                'description' => 'Rooftop operating hours end time',
                'group' => 'rooftop',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'rooftop_terms_conditions',
                'value' => 'Standard terms and conditions for rooftop reservations apply. No smoking, no excessive noise after 10 PM, cleaning is mandatory.',
                'type' => 'text',
                'description' => 'Terms and conditions for rooftop reservations',
                'group' => 'rooftop',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'rooftop_cancellation_policy',
                'value' => 'Cancellations must be made at least 48 hours in advance for full refund. Cancellations within 48 hours will forfeit 50% of the deposit.',
                'type' => 'text',
                'description' => 'Cancellation policy for rooftop reservations',
                'group' => 'rooftop',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insertOrIgnore($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'rooftop_base_rate',
            'rooftop_hourly_rate',
            'rooftop_cleaning_fee',
            'rooftop_security_deposit',
            'rooftop_max_guests',
            'rooftop_advance_booking_days',
            'rooftop_operating_hours_start',
            'rooftop_operating_hours_end',
            'rooftop_terms_conditions',
            'rooftop_cancellation_policy'
        ])->delete();
    }
};
