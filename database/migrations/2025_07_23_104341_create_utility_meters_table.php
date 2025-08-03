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
        Schema::create('utility_meters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments')->onDelete('cascade');
            $table->enum('type', ['electricity', 'water', 'gas'])->default('electricity');
            $table->string('meter_number')->unique();
            $table->decimal('last_reading', 10, 2)->default(0);
            $table->date('last_reading_date')->nullable();
            $table->decimal('rate_per_unit', 8, 4)->default(0); // Rate per unit (kWh, gallon, etc.)
            $table->enum('status', ['active', 'inactive', 'faulty'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['apartment_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utility_meters');
    }
};
