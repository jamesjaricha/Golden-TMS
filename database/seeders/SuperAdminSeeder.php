<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        User::updateOrCreate(
            ['email' => 'jarichajames1@gmail.com'],
            [
                'name' => 'James Jaricha',
                'email' => 'jarichajames1@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super Admin user created successfully!');
        $this->command->info('Email: jarichajames1@gmail.com');
        $this->command->info('Password: password');
        $this->command->warn('Please change the password after first login!');
    }
}
