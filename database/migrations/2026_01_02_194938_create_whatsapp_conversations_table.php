<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This table tracks WhatsApp conversation state for the ticket creation wizard.
     * Each conversation goes through steps: greeting -> client_name -> phone -> location -> branch -> issue -> confirm
     */
    public function up(): void
    {
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 30)->index(); // WhatsApp number
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('current_step', 50)->default('idle'); // idle, client_name, client_phone, location, branch, department, issue, priority, confirm
            $table->json('collected_data')->nullable(); // Stores collected ticket data as JSON
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_ticket_id')->nullable()->constrained('complaints')->nullOnDelete();
            $table->timestamps();

            // Each phone number can only have one active conversation
            $table->unique('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversations');
    }
};
