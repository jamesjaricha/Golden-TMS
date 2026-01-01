<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Billing', 'description' => 'Department for Billing', 'is_active' => true],
            ['name' => 'Claims', 'description' => 'Department for Claims', 'is_active' => true],
            ['name' => 'IT Support', 'description' => 'Department for IT Support', 'is_active' => true],
            ['name' => 'Customer Service', 'description' => 'Department for Customer Service', 'is_active' => true],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['name' => $dept['name']],
                $dept
            );
        }

        $this->command->info('Departments seeded successfully!');
    }
}
