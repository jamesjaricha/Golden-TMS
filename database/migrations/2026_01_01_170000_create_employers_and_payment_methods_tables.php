<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create employers table
        Schema::create('employers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed default employers
        $now = now();
        DB::table('employers')->insert([
            ['name' => 'ZNA', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'ZPS', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'ZRP', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Self Employed', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Private Sector', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Create payment methods table
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed default payment methods
        DB::table('payment_methods')->insert([
            ['name' => 'CASH', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'ECOCASH', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'INNBUCKS', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'BANK STOP ORDER', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'SWIPE', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Add columns to complaints table
        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('employer_id')->nullable()->after('branch_id')->constrained('employers');
            $table->foreignId('payment_method_id')->nullable()->after('employer_id')->constrained('payment_methods');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['employer_id']);
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['employer_id', 'payment_method_id']);
        });

        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('employers');
    }
};
