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
        Schema::create('management_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments')->onDelete('cascade');
            $table->decimal('area_sqft', 8, 2); // Apartment area in square feet
            $table->decimal('management_fee_ratio', 8, 2)->default(14.00); // Current ratio for management fee
            $table->decimal('sinking_fund_ratio', 8, 2)->default(2.50); // Current ratio for sinking fund
            
            // Monthly calculations
            $table->decimal('monthly_management_fee', 10, 2); // Area x Management Fee Ratio
            $table->decimal('monthly_sinking_fund', 10, 2); // Area x Sinking Fund Ratio
            
            // Quarterly calculations (Monthly x 3)
            $table->decimal('quarterly_management_fee', 10, 2); // Monthly Management Fee x 3
            $table->decimal('quarterly_sinking_fund', 10, 2); // Monthly Sinking Fund x 3
            $table->decimal('total_quarterly_rental', 10, 2); // Total Management Rental
            
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->date('effective_from'); // When this fee structure became effective
            $table->date('effective_until')->nullable(); // When this fee structure expires
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['apartment_id', 'status']);
            $table->index(['effective_from', 'effective_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('management_fees');
    }
};
