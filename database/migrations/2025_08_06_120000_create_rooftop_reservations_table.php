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
        Schema::create('rooftop_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_hours');
            $table->enum('event_type', ['party', 'wedding', 'corporate', 'family_gathering', 'birthday', 'other']);
            $table->string('event_title');
            $table->text('event_description')->nullable();
            $table->integer('expected_guests')->default(0);
            $table->decimal('base_rate', 10, 2);
            $table->decimal('hourly_rate', 10, 2);
            $table->decimal('cleaning_fee', 10, 2)->default(0);
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->decimal('additional_charges', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->date('deposit_due_date')->nullable();
            $table->date('final_payment_due_date')->nullable();
            $table->text('special_requirements')->nullable();
            $table->json('equipment_requested')->nullable(); // Tables, chairs, sound system, etc.
            $table->json('catering_allowed')->nullable(); // Catering restrictions/permissions
            $table->text('terms_conditions')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->boolean('deposit_paid')->default(false);
            $table->boolean('final_payment_paid')->default(false);
            $table->timestamp('deposit_paid_at')->nullable();
            $table->timestamp('final_payment_paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['reservation_date', 'start_time', 'end_time']);
            $table->index(['owner_id', 'reservation_date']);
            $table->index(['status', 'reservation_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooftop_reservations');
    }
};
