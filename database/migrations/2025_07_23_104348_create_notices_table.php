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
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['general', 'payment', 'maintenance', 'emergency', 'event'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('target_audience', ['all', 'owners', 'owners', 'specific'])->default('all');
            $table->json('specific_recipients')->nullable(); // JSON array of user IDs if target is 'specific'
            $table->foreignId('created_by')->constrained('users');
            $table->date('publish_date')->default(now());
            $table->date('expiry_date')->nullable();
            $table->boolean('is_published')->default(true);
            $table->json('attachments')->nullable(); // JSON array of file paths
            $table->timestamps();

            $table->index(['target_audience', 'is_published', 'publish_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
