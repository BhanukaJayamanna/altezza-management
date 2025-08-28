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
        // Add management_corporation_id to apartments table
        Schema::table('apartments', function (Blueprint $table) {
            $table->foreignId('management_corporation_id')->nullable()->after('owner_id')->constrained('management_corporations')->onDelete('set null');
        });

        // Add management_corporation_id to leases table
        Schema::table('leases', function (Blueprint $table) {
            $table->foreignId('management_corporation_id')->nullable()->after('owner_id')->constrained('management_corporations')->onDelete('set null');
        });

        // Copy existing owner data to management_corporation fields
        DB::statement('UPDATE apartments SET management_corporation_id = owner_id WHERE owner_id IS NOT NULL');
        DB::statement('UPDATE leases SET management_corporation_id = owner_id WHERE owner_id IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropForeign(['management_corporation_id']);
            $table->dropColumn('management_corporation_id');
        });

        Schema::table('leases', function (Blueprint $table) {
            $table->dropForeign(['management_corporation_id']);
            $table->dropColumn('management_corporation_id');
        });
    }
};
