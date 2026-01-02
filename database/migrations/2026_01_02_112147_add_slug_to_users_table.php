<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        // Generate slugs for existing users
        $users = \App\Models\User::withTrashed()->get();
        foreach ($users as $user) {
            $baseSlug = Str::slug($user->name);
            $slug = $baseSlug;
            $counter = 1;

            while (\App\Models\User::withTrashed()->where('slug', $slug)->where('id', '!=', $user->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $user->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
