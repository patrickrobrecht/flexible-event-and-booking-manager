<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Create table for {@see \App\Models\FormFieldGroup}s.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('form_field_groups', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms');
            $table->integer('sort');
            $table->string('name');
            $table->boolean('show_name');
            $table->text('description');
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
        Schema::dropIfExists('form_field_groups');
    }
};
