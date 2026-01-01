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
        Schema::create('whatsapp_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // incoming, message_received, message_sent, status_update
            $table->text('payload'); // JSON payload from webhook
            $table->string('message_id')->nullable(); // WhatsApp message ID
            $table->string('status')->nullable(); // sent, delivered, read, failed
            $table->string('phone_number')->nullable(); // Sender/recipient phone
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('message_id');
            $table->index('phone_number');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_webhook_logs');
    }
};
