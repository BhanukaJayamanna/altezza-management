<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename columns to match expected names
        if (Schema::hasColumn('management_fee_invoices', 'period_start') && !Schema::hasColumn('management_fee_invoices', 'billing_period_start')) {
            Schema::table('management_fee_invoices', function (Blueprint $table) {
                $table->renameColumn('period_start', 'billing_period_start');
            });
        }

        if (Schema::hasColumn('management_fee_invoices', 'period_end') && !Schema::hasColumn('management_fee_invoices', 'billing_period_end')) {
            Schema::table('management_fee_invoices', function (Blueprint $table) {
                $table->renameColumn('period_end', 'billing_period_end');
            });
        }

        if (Schema::hasColumn('management_fee_invoices', 'square_footage') && !Schema::hasColumn('management_fee_invoices', 'area_sqft')) {
            Schema::table('management_fee_invoices', function (Blueprint $table) {
                $table->renameColumn('square_footage', 'area_sqft');
            });
        }

        if (Schema::hasColumn('management_fee_invoices', 'management_fee_total') && !Schema::hasColumn('management_fee_invoices', 'quarterly_management_fee')) {
            Schema::table('management_fee_invoices', function (Blueprint $table) {
                $table->renameColumn('management_fee_total', 'quarterly_management_fee');
            });
        }

        if (Schema::hasColumn('management_fee_invoices', 'sinking_fund_total') && !Schema::hasColumn('management_fee_invoices', 'quarterly_sinking_fund')) {
            Schema::table('management_fee_invoices', function (Blueprint $table) {
                $table->renameColumn('sinking_fund_total', 'quarterly_sinking_fund');
            });
        }

        // Add ratio columns if they don't exist
        if (!Schema::hasColumn('management_fee_invoices', 'management_fee_ratio')) {
            Schema::table('management_fee_invoices', function (Blueprint $table) {
                $table->decimal('management_fee_ratio', 8, 2)->default(14.00)->after('area_sqft');
            });
        }

        if (!Schema::hasColumn('management_fee_invoices', 'sinking_fund_ratio')) {
            Schema::table('management_fee_invoices', function (Blueprint $table) {
                $table->decimal('sinking_fund_ratio', 8, 2)->default(2.50)->after('management_fee_ratio');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't reverse this to avoid data loss
    }
};
