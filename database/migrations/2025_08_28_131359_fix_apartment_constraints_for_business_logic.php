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
        Schema::table('apartments', function (Blueprint $table) {
            // Drop existing foreign key for management_corporation_id only (owner_id doesn't have FK)
            $table->dropForeign(['management_corporation_id']);
            
            // Modify columns to match business requirements
            $table->unsignedBigInteger('owner_id')->nullable()->change();
            $table->unsignedBigInteger('management_corporation_id')->nullable(false)->change();
            
            // Add foreign keys with proper constraints
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('set null');
            $table->foreign('management_corporation_id')->references('id')->on('management_corporations')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            // Drop the foreign keys we added
            $table->dropForeign(['owner_id']);
            $table->dropForeign(['management_corporation_id']);
            
            // Revert columns to original state
            $table->unsignedBigInteger('owner_id')->nullable(false)->change();
            $table->unsignedBigInteger('management_corporation_id')->nullable()->change();
            
            // Restore original foreign key for management_corporation_id only
            $table->foreign('management_corporation_id')->references('id')->on('management_corporations')->onDelete('set null');
        });
    }
};
