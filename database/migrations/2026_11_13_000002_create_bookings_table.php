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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 30)->unique();
            $table->char('request_key', 36)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('field_id')->constrained('fields')->restrictOnDelete();
            $table->dateTime('starts_at'); // Disimpan dalam UTC
            $table->dateTime('ends_at');   // Disimpan dalam UTC
            $table->string('team_name', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('confirmed');
            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancellation_reason', 500)->nullable();
            $table->timestamps();

            $table->index(['field_id', 'status', 'starts_at', 'ends_at'], 'bookings_field_status_window_idx');
            $table->index(['user_id', 'starts_at'], 'bookings_user_starts_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
