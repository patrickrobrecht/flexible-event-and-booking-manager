<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Create table for {@see \App\Models\Form}s.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('forms', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::table('booking_options', static function (Blueprint $table) {
            $table->foreignId('form_id')->nullable()->constrained('forms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('booking_options', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('form_id');
        });
        Schema::dropIfExists('forms');
    }
};
