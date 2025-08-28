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
        // Update payments table - owner_id to owner_id
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'owner_id')) {
                $table->dropForeign(['owner_id']);
                $table->renameColumn('owner_id', 'owner_id');
                $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert payments table
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'owner_id')) {
                $table->dropForeign(['owner_id']);
                $table->renameColumn('owner_id', 'owner_id');
                $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }
};
