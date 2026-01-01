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
        Schema::table('complaints', function (Blueprint $table) {
            // Index for status filtering (most common filter)
            $table->index('status', 'idx_complaints_status');

            // Index for priority filtering
            $table->index('priority', 'idx_complaints_priority');

            // Index for date range filtering on created_at
            $table->index('created_at', 'idx_complaints_created_at');

            // Index for assigned_to lookups
            $table->index('assigned_to', 'idx_complaints_assigned_to');

            // Index for captured_by lookups (user role filtering)
            $table->index('captured_by', 'idx_complaints_captured_by');

            // Composite index for common filter combinations
            $table->index(['status', 'created_at'], 'idx_complaints_status_created');
            $table->index(['status', 'priority'], 'idx_complaints_status_priority');

            // Index for search on policy_number (frequently searched)
            $table->index('policy_number', 'idx_complaints_policy_number');

            // Note: ticket_number already has unique index
            // phone_number and full_name will be handled by full-text search in future
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropIndex('idx_complaints_status');
            $table->dropIndex('idx_complaints_priority');
            $table->dropIndex('idx_complaints_created_at');
            $table->dropIndex('idx_complaints_assigned_to');
            $table->dropIndex('idx_complaints_captured_by');
            $table->dropIndex('idx_complaints_status_created');
            $table->dropIndex('idx_complaints_status_priority');
            $table->dropIndex('idx_complaints_policy_number');
        });
    }
};
