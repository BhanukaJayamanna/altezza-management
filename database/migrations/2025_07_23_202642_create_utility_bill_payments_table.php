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
        Schema::create('utility_bill_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('utility_bills')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('paid_on');
            $table->enum('payment_method', ['cash', 'check', 'bank_transfer', 'credit_card', 'online'])->default('cash');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();

            $table->index(['bill_id', 'paid_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utility_bill_payments');
    }
};
