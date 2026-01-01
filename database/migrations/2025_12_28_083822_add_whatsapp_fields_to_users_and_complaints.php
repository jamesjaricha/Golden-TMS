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
            $table->string('whatsapp_phone', 20)->nullable()->after('phone_number');
            $table->boolean('whatsapp_notifications_enabled')->default(true)->after('whatsapp_phone');
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->string('customer_satisfaction')->nullable()->after('status'); // satisfied, unsatisfied
            $table->timestamp('satisfaction_recorded_at')->nullable()->after('customer_satisfaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_phone', 'whatsapp_notifications_enabled']);
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['customer_satisfaction', 'satisfaction_recorded_at']);
        });
    }
};
