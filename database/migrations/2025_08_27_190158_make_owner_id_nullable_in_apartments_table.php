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
            // Drop the foreign key constraint first
            $table->dropForeign(['owner_id']);
            
            // Drop the owner_id column completely since apartments are now managed by management corporations
            $table->dropColumn('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            // Re-add owner_id column
            $table->foreignId('owner_id')->after('description');
            
            // Add foreign key constraint (this would need to be adjusted based on current system)
            // Note: This rollback might not work if owners table no longer exists
            $table->foreign('owner_id')->references('id')->on('management_corporations')->onDelete('cascade');
        });
    }
};
