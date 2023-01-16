<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Create table for {@see \App\Models\FormField}s.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('form_fields', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_field_group_id')->constrained('form_field_groups');
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
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
