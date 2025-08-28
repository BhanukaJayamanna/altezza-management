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
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('apartment_id')->nullable()->constrained('apartments')->onDelete('set null');
            $table->date('lease_start')->nullable();
            $table->date('lease_end')->nullable();
            $table->enum('status', ['active', 'inactive', 'moved_out'])->default('active');
            $table->string('id_document')->nullable(); // ID/Passport file path
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'lease_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owners');
    }
};
