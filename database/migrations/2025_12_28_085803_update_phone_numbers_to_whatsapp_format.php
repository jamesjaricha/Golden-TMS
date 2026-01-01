<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing phone numbers to WhatsApp international format
        DB::table('complaints')->get()->each(function ($complaint) {
            $phone = $complaint->phone_number;

            // Remove all non-numeric characters
            $phone = preg_replace('/\D/', '', $phone);

            // If starts with 0, replace with 263
            if (str_starts_with($phone, '0')) {
                $phone = '263' . substr($phone, 1);
            }
            // If doesn't start with 263, add it
            elseif (!str_starts_with($phone, '263')) {
                $phone = '263' . $phone;
            }

            // Ensure it's 12 digits
            if (strlen($phone) > 12) {
                $phone = substr($phone, 0, 12);
            }

            // Update the record
            DB::table('complaints')
                ->where('id', $complaint->id)
                ->update(['phone_number' => $phone]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action needed
    }
};
