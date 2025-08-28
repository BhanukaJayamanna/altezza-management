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
        // Make potentially problematic columns nullable or add defaults
        Schema::table('management_fee_invoices', function (Blueprint $table) {
            // Make columns nullable if they exist and have constraints
            if (Schema::hasColumn('management_fee_invoices', 'management_fee_monthly')) {
                $table->decimal('management_fee_monthly', 10, 2)->nullable()->default(0)->change();
            }
            if (Schema::hasColumn('management_fee_invoices', 'sinking_fund_monthly')) {
                $table->decimal('sinking_fund_monthly', 10, 2)->nullable()->default(0)->change();
            }
            if (Schema::hasColumn('management_fee_invoices', 'assessment_no')) {
                $table->string('assessment_no')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
