<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 20)->nullable()->unique();
            $table->string('location')->nullable();
            $table->timestamps();
        });

        // Seed all branches
        $now = now();
        $branches = [
            ['name' => 'Harare HQ', 'code' => 'HAR', 'location' => 'Harare', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Beitbridge', 'code' => 'BBG', 'location' => 'Beitbridge', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bindura', 'code' => 'BND', 'location' => 'Bindura', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bulawayo', 'code' => 'BYO', 'location' => 'Bulawayo', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chinhoyi', 'code' => 'CHY', 'location' => 'Chinhoyi', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chipinge', 'code' => 'CPG', 'location' => 'Chipinge', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chiredzi', 'code' => 'CDZ', 'location' => 'Chiredzi', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chitungwiza', 'code' => 'CTZ', 'location' => 'Chitungwiza', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Epworth', 'code' => 'EPW', 'location' => 'Epworth', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gokwe', 'code' => 'GKW', 'location' => 'Gokwe', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Guruve', 'code' => 'GRV', 'location' => 'Guruve', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gwanda', 'code' => 'GWD', 'location' => 'Gwanda', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gweru', 'code' => 'GWE', 'location' => 'Gweru', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kadoma', 'code' => 'KAD', 'location' => 'Kadoma', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Karoi', 'code' => 'KAR', 'location' => 'Karoi', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kwekwe', 'code' => 'KWK', 'location' => 'Kwekwe', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Marondera', 'code' => 'MRD', 'location' => 'Marondera', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Masvingo', 'code' => 'MAS', 'location' => 'Masvingo', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mutare', 'code' => 'MUT', 'location' => 'Mutare', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mutoko', 'code' => 'MTK', 'location' => 'Mutoko', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ngezi', 'code' => 'NGZ', 'location' => 'Ngezi', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Nyanga', 'code' => 'NYG', 'location' => 'Nyanga', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rusape', 'code' => 'RSP', 'location' => 'Rusape', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Zvishavane', 'code' => 'ZVS', 'location' => 'Zvishavane', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('branches')->insert($branches);

        Schema::create('branch_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['branch_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_user');
        Schema::dropIfExists('branches');
    }
};
