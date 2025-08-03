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
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique(); // auto-generated PV-2025-0001
            $table->date('voucher_date');
            $table->string('vendor_name');
            $table->string('vendor_phone')->nullable();
            $table->string('vendor_email')->nullable();
            $table->text('vendor_address')->nullable();
            $table->text('description');
            $table->decimal('amount', 12, 2);
            $table->foreignId('apartment_id')->nullable()->constrained('apartments')->onDelete('set null'); // optional: specific apartment
            $table->string('expense_category')->default('general'); // maintenance, utility, supplies, services, etc.
            $table->enum('payment_method', ['cash', 'cheque', 'bank_transfer', 'online', 'card'])->default('cash');
            $table->string('reference_number')->nullable(); // cheque number, transaction ID, etc.
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->string('receipt_file')->nullable(); // uploaded receipt/invoice
            $table->date('payment_date')->nullable(); // when actually paid
            $table->timestamps();
            
            $table->index(['voucher_date', 'status']);
            $table->index(['vendor_name']);
            $table->index(['expense_category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};
