<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fixes the status enum to include 'partial_closed'
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE complaints MODIFY COLUMN status ENUM('pending', 'assigned', 'in_progress', 'resolved', 'closed', 'escalated', 'partial_closed') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE complaints MODIFY COLUMN status ENUM('pending', 'assigned', 'in_progress', 'resolved', 'closed', 'escalated') DEFAULT 'pending'");
        }
    }
};
