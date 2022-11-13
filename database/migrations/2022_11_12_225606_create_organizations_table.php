<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Create table for {@see \App\Models\Organization}s.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('organizations', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('status')->index();
            $table->string('register_entry')->nullable();
            $table->string('representatives')->nullable();
            $table->string('website_url')->nullable();
            $table->foreignId('parent_organization_id')->nullable()->constrained('organizations');
            $table->foreignId('location_id')->constrained('locations');
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
        Schema::dropIfExists('organizations');
    }
};
