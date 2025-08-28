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
        // Remove redundant owner_id from apartments table
        // The current owner should be determined through active leases only
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });

        // Remove redundant apartment_id from owners table
        // Owner's apartment should be determined through active lease only
        Schema::table('owners', function (Blueprint $table) {
            $table->dropForeign(['apartment_id']);  
            $table->dropColumn('apartment_id');
            $table->dropColumn('lease_start');
            $table->dropColumn('lease_end');
        });

        // Add index to leases table for better performance
        Schema::table('leases', function (Blueprint $table) {
            $table->index(['owner_id', 'status']);
            $table->index(['apartment_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore owner_id to apartments table
        Schema::table('apartments', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
        });

        // Restore apartment_id to owners table
        Schema::table('owners', function (Blueprint $table) {
            $table->foreignId('apartment_id')->nullable()->constrained('apartments')->onDelete('set null');
            $table->date('lease_start')->nullable();
            $table->date('lease_end')->nullable();
        });

        // Remove added indexes
        Schema::table('leases', function (Blueprint $table) {
            $table->dropIndex(['owner_id', 'status']);
            $table->dropIndex(['apartment_id', 'status']);
        });
    }
};
