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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // ticket_assigned, ticket_updated, ticket_resolved, etc.
            $table->string('title');
            $table->text('message');
            $table->string('url')->nullable(); // Link to the ticket/resource
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('data')->nullable(); // Additional data like ticket_id, etc.
            $table->timestamps();

            $table->index(['user_id', 'read', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
