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
        // 1. Rename owners table to owners
        Schema::rename('owners', 'owners');
        
        // 2. Update foreign key references throughout the database
        
        // Update apartments table - owner_id to owner_id
        Schema::table('apartments', function (Blueprint $table) {
            if (Schema::hasColumn('apartments', 'owner_id')) {
                $table->renameColumn('owner_id', 'owner_id');
            }
        });
        
        // Update invoices table - owner_id to owner_id
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'owner_id')) {
                $table->dropForeign(['owner_id']);
                $table->renameColumn('owner_id', 'owner_id');
                $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
        
        // Update maintenance_requests table - owner_id to owner_id
        Schema::table('maintenance_requests', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_requests', 'owner_id')) {
                $table->dropForeign(['owner_id']);
                $table->renameColumn('owner_id', 'owner_id');
                $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
        
        // Update complaints table - owner_id to owner_id
        Schema::table('complaints', function (Blueprint $table) {
            if (Schema::hasColumn('complaints', 'owner_id')) {
                $table->dropForeign(['owner_id']);
                $table->renameColumn('owner_id', 'owner_id');
                $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
        
        // Update utility_bills table - owner_id to owner_id
        Schema::table('utility_bills', function (Blueprint $table) {
            if (Schema::hasColumn('utility_bills', 'owner_id')) {
                $table->dropForeign(['owner_id']);
                $table->dropIndex(['owner_id', 'status']);
                $table->renameColumn('owner_id', 'owner_id');
                $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['owner_id', 'status']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse all the changes
        
        // Revert utility_bills table
        Schema::table('utility_bills', function (Blueprint $table) {
            if (Schema::hasColumn('utility_bills', 'owner_id')) {
                $table->dropForeign(['owner_id']);
                $table->dropIndex(['owner_id', 'status']);
                $table->renameColumn('owner_id', 'owner_id');
                $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['owner_id', 'status']);
            }
        });
        
        // Revert complaints table
        Schema::table('complaints', function (Blueprint $table) {
            if (Schema::hasColumn('complaints', 'owner_id')) {
                $table->dropForeign(['owner_id']);
                $table->renameColumn('owner_id', 'owner_id');
                $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
        
        // Revert maintenance_requests table
        Schema::table('maintenance_requests', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_requests', 'owner_id')) {
                $table->dropForeign(['owner_id']);
                $table->renameColumn('owner_id', 'owner_id');
                $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
        
        // Revert invoices table
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'owner_id')) {
                $table->dropForeign(['owner_id']);
                $table->renameColumn('owner_id', 'owner_id');
                $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
        
        // Revert apartments table
        Schema::table('apartments', function (Blueprint $table) {
            if (Schema::hasColumn('apartments', 'owner_id')) {
                $table->renameColumn('owner_id', 'owner_id');
            }
        });
        
        // Rename owners table back to owners
        Schema::rename('owners', 'owners');
    }
};
