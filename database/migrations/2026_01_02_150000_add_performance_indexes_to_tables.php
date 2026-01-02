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
            // For MySQL/MariaDB, use raw SQL to safely create indexes only if they don't exist
            $this->createIndexIfNotExists('complaints', 'complaints_status_index', 'status');
            $this->createIndexIfNotExists('complaints', 'complaints_priority_index', 'priority');
            $this->createIndexIfNotExists('complaints', 'complaints_created_at_index', 'created_at');
            $this->createIndexIfNotExists('complaints', 'complaints_status_created_at_index', 'status, created_at');
            $this->createIndexIfNotExists('complaints', 'complaints_assigned_to_status_index', 'assigned_to, status');
            $this->createIndexIfNotExists('complaints', 'complaints_captured_by_created_at_index', 'captured_by, created_at');

            $this->createIndexIfNotExists('notifications', 'notifications_user_read_index', 'user_id, `read`');

            if (Schema::hasTable('whatsapp_messages')) {
                $this->createIndexIfNotExists('whatsapp_messages', 'whatsapp_messages_status_index', 'status');
                $this->createIndexIfNotExists('whatsapp_messages', 'whatsapp_messages_from_number_index', 'from_number');
            }
        }
    }

    /**
     * Helper to create index if it doesn't exist (MySQL/MariaDB)
     */
    private function createIndexIfNotExists(string $table, string $indexName, string $columns): void
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        if (empty($indexes)) {
            DB::statement("CREATE INDEX `{$indexName}` ON `{$table}` ({$columns})");
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
