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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Who performed the action
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name')->nullable(); // Stored separately in case user is deleted
            $table->string('user_email')->nullable();
            $table->string('user_role')->nullable();

            // What action was performed
            $table->string('action'); // create, update, delete, login, logout, export, view, etc.
            $table->string('action_category'); // auth, ticket, user, report, system

            // What entity was affected
            $table->string('auditable_type')->nullable(); // Model class name
            $table->unsignedBigInteger('auditable_id')->nullable(); // Model ID
            $table->string('auditable_identifier')->nullable(); // Human-readable ID (e.g., ticket number)

            // What changed (for updates)
            $table->json('old_values')->nullable(); // Before state
            $table->json('new_values')->nullable(); // After state
            $table->json('changed_fields')->nullable(); // List of changed field names

            // Human-readable description
            $table->text('description');

            // Additional context
            $table->json('metadata')->nullable(); // Extra data (filters used, export format, etc.)

            // Request information
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->string('browser')->nullable();
            $table->string('platform')->nullable(); // Windows, Mac, Linux, iOS, Android

            // Session tracking
            $table->string('session_id')->nullable();

            // Status of the action
            $table->enum('status', ['success', 'failed', 'warning'])->default('success');
            $table->text('failure_reason')->nullable();

            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['user_id', 'created_at']);
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['action', 'created_at']);
            $table->index(['action_category', 'created_at']);
            $table->index('session_id');
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
