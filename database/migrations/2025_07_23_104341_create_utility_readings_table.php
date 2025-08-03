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
        Schema::create('utility_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')->constrained('utility_meters')->onDelete('cascade');
            $table->decimal('current_reading', 10, 2);
            $table->decimal('previous_reading', 10, 2)->default(0);
            $table->decimal('consumption', 10, 2); // current - previous
            $table->decimal('amount', 10, 2)->default(0); // consumption * rate
            $table->date('reading_date');
            $table->date('billing_period_start');
            $table->date('billing_period_end');
            $table->foreignId('recorded_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['meter_id', 'reading_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utility_readings');
    }
};
