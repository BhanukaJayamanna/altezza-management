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
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // Apartment number
            $table->string('block')->nullable(); // Block/Tower
            $table->integer('floor')->nullable();
            $table->enum('type', ['1bhk', '2bhk', '3bhk', '4bhk', 'studio', 'penthouse'])->default('1bhk');
            $table->enum('status', ['vacant', 'occupied', 'maintenance'])->default('vacant');
            $table->decimal('area', 8, 2)->nullable(); // Square feet/meters
            $table->decimal('rent_amount', 10, 2)->nullable(); // Monthly rent
            $table->text('description')->nullable();
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['status', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
