<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only add whatsapp_number if it doesn't exist
            if (!Schema::hasColumn('users', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable()->after('email');
            }
            // whatsapp_notifications_enabled is already added by 2025_12_28_083822 migration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'whatsapp_number')) {
                $table->dropColumn('whatsapp_number');
            }
        });
    }
};
