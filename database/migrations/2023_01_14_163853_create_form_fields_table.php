<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Create table for {@see \App\Models\FormField}s.
     */
    public function up(): void
    {
        Schema::create('form_fields', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_option_id')->constrained('booking_options');
            $table->integer('sort');
            $table->string('name');
            $table->text('hint')->nullable();
            $table->string('container_class')->nullable();
            $table->string('column')->nullable();
            $table->string('type');
            $table->boolean('required');
            $table->json('validation_rules')->nullable();
            $table->json('allowed_values')->nullable();
            $table->boolean('editable_after_submission');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
