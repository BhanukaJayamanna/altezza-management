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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who receives the notification
            $table->string('type'); // payment, maintenance, lease, overdue, user_activity
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data (apartment_id, payment_amount, etc.)
            $table->string('icon')->default('bell'); // Icon identifier
            $table->string('color')->default('blue'); // Color theme (blue, green, amber, red, purple)
            $table->timestamp('read_at')->nullable(); // When notification was read
            $table->string('action_url')->nullable(); // Link to relevant page
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
