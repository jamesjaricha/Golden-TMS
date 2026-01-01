<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Reset password for existing user
        DB::table('users')
            ->where('email', 'jarichajames1@gmail.com')
            ->update([
                'password' => Hash::make('Password123'),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // No rollback
    }
};
