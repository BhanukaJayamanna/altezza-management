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
        Schema::create('utility_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('apartment_id')->constrained('apartments')->onDelete('cascade');
            $table->foreignId('meter_id')->constrained('utility_meters')->onDelete('cascade');
            $table->foreignId('reading_id')->constrained('utility_readings')->onDelete('cascade');
            $table->enum('type', ['electricity', 'water', 'gas']);
            $table->string('period'); // Format: MM/YYYY (e.g., "01/2024")
            $table->integer('month');
            $table->integer('year');
            $table->decimal('units_used', 10, 2);
            $table->decimal('price_per_unit', 8, 4);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['unpaid', 'paid', 'partial', 'overdue'])->default('unpaid');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'status']);
            $table->index(['apartment_id', 'period']);
            $table->unique(['meter_id', 'period']); // One bill per meter per period
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utility_bills');
    }
};
