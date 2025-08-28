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
        Schema::create('management_fee_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('apartment_id')->constrained('apartments')->onDelete('cascade');
            $table->foreignId('management_fee_id')->constrained('management_fees')->onDelete('cascade');
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Billing period (quarterly)
            $table->date('billing_period_start');
            $table->date('billing_period_end');
            $table->integer('quarter'); // 1, 2, 3, or 4
            $table->year('year');
            
            // Fee breakdown
            $table->decimal('area_sqft', 8, 2);
            $table->decimal('management_fee_ratio', 8, 2);
            $table->decimal('sinking_fund_ratio', 8, 2);
            $table->decimal('quarterly_management_fee', 10, 2);
            $table->decimal('quarterly_sinking_fund', 10, 2);
            $table->decimal('total_amount', 10, 2);
            
            // Additional charges
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('net_total', 10, 2); // Total after late fee and discount
            
            // Payment tracking
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->date('due_date');
            $table->date('paid_on')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Indexes
            $table->index(['apartment_id', 'quarter', 'year']);
            $table->index(['status', 'due_date']);
            $table->index(['owner_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('management_fee_invoices');
    }
};
