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
     * Updates the status CHECK constraint to include 'partial_closed'.
     * Uses database-specific approaches.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: Need to recreate the table
            DB::statement('PRAGMA foreign_keys = OFF');

            // Create a temporary table with the new status values
            DB::statement("
                CREATE TABLE complaints_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    ticket_number VARCHAR NOT NULL,
                    policy_number VARCHAR NOT NULL,
                    full_name VARCHAR NOT NULL,
                    phone_number VARCHAR NOT NULL,
                    location VARCHAR NOT NULL,
                    visited_branch VARCHAR NOT NULL,
                    complaint_text TEXT NOT NULL,
                    status VARCHAR CHECK (status IN ('pending', 'assigned', 'in_progress', 'partial_closed', 'resolved', 'closed', 'escalated')) NOT NULL DEFAULT 'pending',
                    priority VARCHAR CHECK (priority IN ('low', 'medium', 'high', 'urgent')) NOT NULL DEFAULT 'medium',
                    captured_by INTEGER NOT NULL,
                    assigned_to INTEGER,
                    resolved_at DATETIME,
                    partial_closed_at DATETIME,
                    closed_at DATETIME,
                    resolution_notes TEXT,
                    partial_close_notes TEXT,
                    created_at DATETIME,
                    updated_at DATETIME,
                    deleted_at DATETIME,
                    department VARCHAR CHECK (department IN ('Billing', 'Claims', 'IT', 'General Support')) NOT NULL DEFAULT 'General Support',
                    pending_department VARCHAR,
                    completed_department VARCHAR,
                    customer_satisfaction VARCHAR,
                    satisfaction_recorded_at DATETIME,
                    FOREIGN KEY (captured_by) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
                )
            ");

            // Copy data from old table to new table
            DB::statement("
                INSERT INTO complaints_new (
                    id, ticket_number, policy_number, full_name, phone_number, location,
                    visited_branch, complaint_text, status, priority, captured_by, assigned_to,
                    resolved_at, partial_closed_at, closed_at, resolution_notes, partial_close_notes,
                    created_at, updated_at, deleted_at, department, pending_department,
                    completed_department, customer_satisfaction, satisfaction_recorded_at
                )
                SELECT
                    id, ticket_number, policy_number, full_name, phone_number, location,
                    visited_branch, complaint_text, status, priority, captured_by, assigned_to,
                    resolved_at, partial_closed_at, closed_at, resolution_notes, partial_close_notes,
                    created_at, updated_at, deleted_at, department, pending_department,
                    completed_department, customer_satisfaction, satisfaction_recorded_at
                FROM complaints
            ");

            // Drop old table
            DB::statement('DROP TABLE complaints');

            // Rename new table to original name
            DB::statement('ALTER TABLE complaints_new RENAME TO complaints');

            // Recreate indexes
            DB::statement('CREATE UNIQUE INDEX complaints_ticket_number_unique ON complaints(ticket_number)');

            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            // MySQL/MariaDB/PostgreSQL: CHECK constraints are handled differently
            // MySQL 8.0.16+ and MariaDB 10.2.1+ support CHECK constraints but they're often ignored
            // The status validation is handled at the application level (Eloquent model)
            // This migration is a no-op for MySQL as the column already accepts the value
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse would recreate the table without partial_closed
        // Not implementing full reverse as it would lose data
    }
};
