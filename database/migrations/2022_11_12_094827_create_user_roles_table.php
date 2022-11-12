<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Create the table for {@see \App\Models\UserRole}s.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('user_roles', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('abilities')->nullable();
            $table->timestamps();
        });
        Schema::create('user_user_role', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('user_role_id')->constrained('user_roles');
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
        Schema::dropIfExists('user_user_role');
        Schema::dropIfExists('user_roles');
    }
};
