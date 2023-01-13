<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Create table for {@see \App\Models\BookingOption}s.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('booking_options', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedInteger('maximum_bookings')->nullable();
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_until')->nullable();
            $table->unsignedDecimal('price', 8, 2)->nullable();
            $table->json('price_conditions')->nullable();
            $table->json('restrictions')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_options');
    }
};
