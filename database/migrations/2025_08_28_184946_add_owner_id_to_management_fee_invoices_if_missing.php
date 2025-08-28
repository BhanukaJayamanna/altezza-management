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
        // Check if owner_id column exists, if not add it
        if (!Schema::hasColumn('management_fee_invoices', 'owner_id')) {
            Schema::table('management_fee_invoices', function (Blueprint $table) {
                $table->foreignId('owner_id')->nullable()->after('management_fee_id')->constrained('users')->onDelete('set null');
            });
        }

        // Check and add other missing columns
        if (!Schema::hasColumn('management_fee_invoices', 'management_fee_id')) {
            Schema::table('management_fee_invoices', function (Blueprint $table) {
                $table->foreignId('management_fee_id')->nullable()->after('apartment_id')->constrained('management_fees')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('management_fee_invoices', 'late_fee')) {
            Schema::table('management_fee_invoices', function (Blueprint $table) {
                $table->decimal('late_fee', 10, 2)->default(0)->after('total_amount');
            });
        }

        if (!Schema::hasColumn('management_fee_invoices', 'discount')) {
            Schema::table('management_fee_invoices', function (Blueprint $table) {
                $table->decimal('discount', 10, 2)->default(0)->after('late_fee');
            });
        }

        if (!Schema::hasColumn('management_fee_invoices', 'net_total')) {
            Schema::table('management_fee_invoices', function (Blueprint $table) {
                $table->decimal('net_total', 10, 2)->after('discount');
            });
            
            // Calculate net_total for existing records
            DB::statement('UPDATE management_fee_invoices SET net_total = total_amount + COALESCE(late_fee, 0) - COALESCE(discount, 0)');
        }

        if (!Schema::hasColumn('management_fee_invoices', 'created_by')) {
            Schema::table('management_fee_invoices', function (Blueprint $table) {
                $table->foreignId('created_by')->default(1)->after('notes')->constrained('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't reverse this migration to avoid data loss
    }
};
