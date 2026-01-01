<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Department;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, map old enum values to new department IDs
        $departmentMap = [
            'Billing' => Department::where('name', 'Billing')->first()?->id,
            'Claims' => Department::where('name', 'Claims')->first()?->id,
            'IT' => Department::where('name', 'IT Support')->first()?->id,
            'General Support' => Department::where('name', 'Customer Service')->first()?->id,
        ];

        // Add new department_id column only if it doesn't exist
        if (!Schema::hasColumn('complaints', 'department_id')) {
            Schema::table('complaints', function (Blueprint $table) {
                $table->unsignedBigInteger('department_id')->nullable()->after('visited_branch');
            });
        }

        // Migrate existing data only if department_id is null
        foreach ($departmentMap as $oldValue => $newId) {
            if ($newId) {
                DB::table('complaints')
                    ->where('department', $oldValue)
                    ->whereNull('department_id')
                    ->update(['department_id' => $newId]);
            }
        }

        // Add foreign key (try/catch for SQLite compatibility)
        try {
            Schema::table('complaints', function (Blueprint $table) {
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict');
            });
        } catch (\Exception $e) {
            // Foreign key might already exist
        }

        // Drop old enum column if it still exists
        if (Schema::hasColumn('complaints', 'department')) {
            Schema::table('complaints', function (Blueprint $table) {
                $table->dropColumn('department');
            });
        }
    }    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Re-add enum column
            $table->enum('department', ['Billing', 'Claims', 'IT', 'General Support'])->default('General Support')->after('visited_branch');
        });

        // Migrate data back
        $departments = Department::all();
        foreach ($departments as $dept) {
            $oldValue = match($dept->name) {
                'Billing' => 'Billing',
                'Claims' => 'Claims',
                'IT Support' => 'IT',
                'Customer Service' => 'General Support',
                default => 'General Support',
            };

            DB::table('complaints')
                ->where('department_id', $dept->id)
                ->update(['department' => $oldValue]);
        }

        Schema::table('complaints', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
};
