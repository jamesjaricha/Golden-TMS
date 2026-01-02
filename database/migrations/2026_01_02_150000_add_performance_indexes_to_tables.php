<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PERFORMANCE: Add indexes for columns frequently used in WHERE, ORDER BY, and JOIN clauses
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // For SQLite, use CREATE INDEX IF NOT EXISTS
        if ($driver === 'sqlite') {
            // Complaints table indexes
            DB::statement('CREATE INDEX IF NOT EXISTS complaints_status_index ON complaints (status)');
            DB::statement('CREATE INDEX IF NOT EXISTS complaints_priority_index ON complaints (priority)');
            DB::statement('CREATE INDEX IF NOT EXISTS complaints_created_at_index ON complaints (created_at)');
            DB::statement('CREATE INDEX IF NOT EXISTS complaints_status_created_at_index ON complaints (status, created_at)');
            DB::statement('CREATE INDEX IF NOT EXISTS complaints_assigned_to_status_index ON complaints (assigned_to, status)');
            DB::statement('CREATE INDEX IF NOT EXISTS complaints_captured_by_created_at_index ON complaints (captured_by, created_at)');

            // Notifications table indexes
            DB::statement('CREATE INDEX IF NOT EXISTS notifications_user_read_index ON notifications (user_id, read)');

            // WhatsApp messages table indexes (if table exists)
            if (Schema::hasTable('whatsapp_messages')) {
                DB::statement('CREATE INDEX IF NOT EXISTS whatsapp_messages_status_index ON whatsapp_messages (status)');
                DB::statement('CREATE INDEX IF NOT EXISTS whatsapp_messages_from_number_index ON whatsapp_messages (from_number)');
            }
        } else {
            // For MySQL/PostgreSQL, use standard Laravel schema with error handling
            Schema::table('complaints', function (Blueprint $table) {
                $table->index('status', 'complaints_status_index');
                $table->index('priority', 'complaints_priority_index');
                $table->index('created_at', 'complaints_created_at_index');
                $table->index(['status', 'created_at'], 'complaints_status_created_at_index');
                $table->index(['assigned_to', 'status'], 'complaints_assigned_to_status_index');
                $table->index(['captured_by', 'created_at'], 'complaints_captured_by_created_at_index');
            });

            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['user_id', 'read'], 'notifications_user_read_index');
            });

            if (Schema::hasTable('whatsapp_messages')) {
                Schema::table('whatsapp_messages', function (Blueprint $table) {
                    $table->index('status', 'whatsapp_messages_status_index');
                    $table->index('from_number', 'whatsapp_messages_from_number_index');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS complaints_status_index');
            DB::statement('DROP INDEX IF EXISTS complaints_priority_index');
            DB::statement('DROP INDEX IF EXISTS complaints_created_at_index');
            DB::statement('DROP INDEX IF EXISTS complaints_status_created_at_index');
            DB::statement('DROP INDEX IF EXISTS complaints_assigned_to_status_index');
            DB::statement('DROP INDEX IF EXISTS complaints_captured_by_created_at_index');
            DB::statement('DROP INDEX IF EXISTS notifications_user_read_index');

            if (Schema::hasTable('whatsapp_messages')) {
                DB::statement('DROP INDEX IF EXISTS whatsapp_messages_status_index');
                DB::statement('DROP INDEX IF EXISTS whatsapp_messages_from_number_index');
            }
        } else {
            Schema::table('complaints', function (Blueprint $table) {
                $table->dropIndex('complaints_status_index');
                $table->dropIndex('complaints_priority_index');
                $table->dropIndex('complaints_created_at_index');
                $table->dropIndex('complaints_status_created_at_index');
                $table->dropIndex('complaints_assigned_to_status_index');
                $table->dropIndex('complaints_captured_by_created_at_index');
            });

            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex('notifications_user_read_index');
            });

            if (Schema::hasTable('whatsapp_messages')) {
                Schema::table('whatsapp_messages', function (Blueprint $table) {
                    $table->dropIndex('whatsapp_messages_status_index');
                    $table->dropIndex('whatsapp_messages_from_number_index');
                });
            }
        }
    }
};
