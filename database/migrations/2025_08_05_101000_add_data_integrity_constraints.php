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
        // Add indexes for performance (MariaDB compatible) - check if they exist first
        Schema::table('leases', function (Blueprint $table) {
            if (!DB::connection()->getSchemaBuilder()->hasIndex('leases', 'leases_status_start_date_end_date_index')) {
                $table->index(['status', 'start_date', 'end_date']);
            }
            if (!DB::connection()->getSchemaBuilder()->hasIndex('leases', 'leases_apartment_id_status_index')) {
                $table->index(['apartment_id', 'status']);
            }
            if (!DB::connection()->getSchemaBuilder()->hasIndex('leases', 'leases_owner_id_status_index')) {
                $table->index(['owner_id', 'status']);
            }
        });

        Schema::table('apartments', function (Blueprint $table) {
            if (!DB::connection()->getSchemaBuilder()->hasIndex('apartments', 'apartments_status_owner_id_index')) {
                $table->index(['status', 'owner_id']);
            }
        });

        Schema::table('owners', function (Blueprint $table) {
            if (!DB::connection()->getSchemaBuilder()->hasIndex('owners', 'owners_status_user_id_index')) {
                $table->index(['status', 'user_id']);
            }
        });

        // Add a trigger or use application-level constraint for unique active leases
        // Note: This would be better handled at the application level
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes
        Schema::table('leases', function (Blueprint $table) {
            $table->dropIndex(['status', 'start_date', 'end_date']);
            $table->dropIndex(['apartment_id', 'status']);
            $table->dropIndex(['owner_id', 'status']);
        });

        Schema::table('apartments', function (Blueprint $table) {
            $table->dropIndex(['status', 'owner_id']);
        });

        Schema::table('owners', function (Blueprint $table) {
            $table->dropIndex(['status', 'user_id']);
        });
    }
};
