<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $user = DB::table('users')->where('email', 'jarichajames1@gmail.com')->first();

        if (!$user) {
            $harareHQ = DB::table('branches')->where('name', 'Harare HQ')->first();

            // Create super admin user
            $userId = DB::table('users')->insertGetId([
                'name' => 'James Jaricha',
                'email' => 'jarichajames1@gmail.com',
                'password' => Hash::make('Password123'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign to Harare HQ
            if ($harareHQ) {
                DB::table('branch_user')->insert([
                    'branch_id' => $harareHQ->id,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            // User exists, just reset password
            DB::table('users')
                ->where('email', 'jarichajames1@gmail.com')
                ->update([
                    'password' => Hash::make('Password123'),
                    'updated_at' => now(),
                ]);
        }
    }    public function down(): void
    {
        DB::table('users')->where('email', 'jarichajames1@gmail.com')->delete();
    }
};
