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
        Schema::table('management_fee_invoices', function (Blueprint $table) {
            // Add missing columns that the service expects
            $table->foreignId('management_fee_id')->nullable()->after('apartment_id')->constrained('management_fees')->onDelete('cascade');
            $table->foreignId('owner_id')->nullable()->after('management_fee_id')->constrained('users')->onDelete('set null');
            
            // Add missing financial columns
            $table->decimal('late_fee', 10, 2)->default(0)->after('total_amount');
            $table->decimal('discount', 10, 2)->default(0)->after('late_fee');
            $table->decimal('net_total', 10, 2)->after('discount');
            
            // Add payment tracking columns
            $table->string('payment_method')->nullable()->after('paid_date');
            $table->string('payment_reference')->nullable()->after('payment_method');
            
            // Add created_by column
            $table->foreignId('created_by')->default(1)->after('notes')->constrained('users');
        });
        
        // Calculate net_total for existing records
        DB::statement('UPDATE management_fee_invoices SET net_total = total_amount + late_fee - discount');
        
        // Update the column names to match what the service expects
        Schema::table('management_fee_invoices', function (Blueprint $table) {
            $table->renameColumn('assessment_number', 'assessment_no');
            $table->renameColumn('period_start', 'billing_period_start');
            $table->renameColumn('period_end', 'billing_period_end');
            $table->renameColumn('square_footage', 'area_sqft');
            $table->renameColumn('management_fee_total', 'quarterly_management_fee');
            $table->renameColumn('sinking_fund_total', 'quarterly_sinking_fund');
            $table->renameColumn('paid_date', 'paid_on');
        });
        
        // Remove the paid_amount column (we'll use net_total instead)
        Schema::table('management_fee_invoices', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('management_fee_invoices', function (Blueprint $table) {
            // Restore original column names
            $table->renameColumn('assessment_no', 'assessment_number');
            $table->renameColumn('billing_period_start', 'period_start');
            $table->renameColumn('billing_period_end', 'period_end');
            $table->renameColumn('area_sqft', 'square_footage');
            $table->renameColumn('quarterly_management_fee', 'management_fee_total');
            $table->renameColumn('quarterly_sinking_fund', 'sinking_fund_total');
            $table->renameColumn('paid_on', 'paid_date');
            
            // Restore paid_amount column
            $table->decimal('paid_amount', 10, 2)->default(0);
            
            // Remove added columns
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
            $table->dropColumn(['payment_method', 'payment_reference']);
            $table->dropColumn(['late_fee', 'discount', 'net_total']);
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
            $table->dropForeign(['management_fee_id']);
            $table->dropColumn('management_fee_id');
        });
    }
};
