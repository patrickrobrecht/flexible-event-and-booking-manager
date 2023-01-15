<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Create table for {@see \App\Models\Booking}s.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bookings', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_option_id')->constrained('booking_options');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();

            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();

            $table->foreignId('booked_by_user_id')->nullable()->constrained('users');
            $table->dateTime('booked_at')->nullable();
            $table->unsignedDecimal('price', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
