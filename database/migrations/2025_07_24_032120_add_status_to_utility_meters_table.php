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
        Schema::table('utility_meters', function (Blueprint $table) {
            // Check if status column doesn't exist before adding it
            if (!Schema::hasColumn('utility_meters', 'status')) {
                $table->enum('status', ['active', 'inactive', 'faulty'])->default('active');
            }
            
            // Also check if rate_per_unit column doesn't exist and add it
            if (!Schema::hasColumn('utility_meters', 'rate_per_unit')) {
                $table->decimal('rate_per_unit', 8, 4)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utility_meters', function (Blueprint $table) {
            if (Schema::hasColumn('utility_meters', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('utility_meters', 'rate_per_unit')) {
                $table->dropColumn('rate_per_unit');
            }
        });
    }
};
