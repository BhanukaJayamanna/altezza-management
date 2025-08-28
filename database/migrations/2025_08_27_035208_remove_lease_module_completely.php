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
        // Remove lease_id foreign key and column from invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['lease_id']);
            $table->dropColumn('lease_id');
        });

        // Drop the leases table completely
        Schema::dropIfExists('leases');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate leases table (basic structure for rollback)
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments')->onDelete('cascade');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('management_corporation_id')->nullable()->constrained('management_corporations')->onDelete('set null');
            $table->string('lease_number')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rent_amount', 10, 2);
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->decimal('maintenance_charge', 10, 2)->default(0);
            $table->text('terms_conditions')->nullable();
            $table->string('contract_file')->nullable();
            $table->enum('status', ['active', 'expired', 'terminated', 'draft'])->default('draft');
            $table->boolean('renewal_notice_sent')->default(false);
            $table->timestamps();
            
            $table->index(['owner_id', 'status']);
        });

        // Add lease_id back to invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('lease_id')->nullable()->after('owner_id')->constrained('leases')->onDelete('set null');
        });
    }
};
