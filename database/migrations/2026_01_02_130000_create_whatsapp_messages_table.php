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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_sid')->unique(); // Twilio message SID
            $table->string('from_number'); // Customer's phone number
            $table->string('to_number'); // Our WhatsApp number
            $table->text('body'); // Message content
            $table->string('profile_name')->nullable(); // WhatsApp profile name
            $table->string('media_url')->nullable(); // If they sent an image/file
            $table->string('media_type')->nullable(); // image/jpeg, etc.
            $table->enum('status', ['new', 'viewed', 'converted', 'archived'])->default('new');
            $table->foreignId('converted_to_ticket_id')->nullable()->constrained('complaints')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('from_number');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
