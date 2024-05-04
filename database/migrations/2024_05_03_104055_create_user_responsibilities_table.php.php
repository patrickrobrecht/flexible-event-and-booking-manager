<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Create polymorhpic relationship between {@see \App\Models\User}s
     * and {@see \App\Models\Traits\HasResponsibleUsers}.
     */
    public function up(): void
    {
        Schema::create('user_responsibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->numericMorphs('responsible_for', 'user_responsibilities_responsible_for_index');
            $table->boolean('publicly_visible');
            $table->string('position')->nullable();
            $table->unsignedInteger('sort')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_responsibilities');
    }
};
