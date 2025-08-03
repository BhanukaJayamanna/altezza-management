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
        Schema::table('invoices', function (Blueprint $table) {
            // Add foreign key to link invoices to utility bills
            $table->foreignId('utility_bill_id')->nullable()->constrained('utility_bills')->onDelete('cascade');
        });

        Schema::table('utility_bills', function (Blueprint $table) {
            // Add foreign key to link utility bills to invoices
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['utility_bill_id']);
            $table->dropColumn('utility_bill_id');
        });

        Schema::table('utility_bills', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
        });
    }
};
