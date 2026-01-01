<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get Harare HQ branch ID
        $harareHQ = DB::table('branches')->where('name', 'Harare HQ')->first();

        if ($harareHQ) {
            // Get all users who don't have any branches assigned
            $usersWithoutBranches = DB::table('users')
                ->leftJoin('branch_user', 'users.id', '=', 'branch_user.user_id')
                ->whereNull('branch_user.user_id')
                ->select('users.id')
                ->get();

            // Assign Harare HQ to all users without branches
            foreach ($usersWithoutBranches as $user) {
                DB::table('branch_user')->insert([
                    'branch_id' => $harareHQ->id,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // No rollback needed - keep branch assignments
    }
};
