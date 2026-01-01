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
     * Adds support for "Partial Closed" status where one department has completed
     * but another department still needs to take action on the ticket.
     */
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Department that still needs to complete their work
            $table->string('pending_department')->nullable()->after('department');

            // Notes explaining what the pending department needs to do
            $table->text('partial_close_notes')->nullable()->after('resolution_notes');

            // Timestamp when ticket was partially closed
            $table->timestamp('partial_closed_at')->nullable()->after('resolved_at');

            // Name of department that completed their work
            $table->string('completed_department')->nullable()->after('pending_department');
        });

        // For SQLite, status is stored as string - validation handled at app level
        // For MySQL/PostgreSQL, you may need to update the enum
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'sqlite') {
            // Uncomment for MySQL:
            // DB::statement("ALTER TABLE complaints MODIFY COLUMN status ENUM('pending', 'assigned', 'in_progress', 'resolved', 'closed', 'escalated', 'partial_closed') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn([
                'pending_department',
                'completed_department',
                'partial_close_notes',
                'partial_closed_at'
            ]);
        });
    }
};
