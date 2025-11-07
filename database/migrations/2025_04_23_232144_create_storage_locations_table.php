<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Create table for {@see App\Models\StorageLocation}s.
     */
    public function up(): void
    {
        Schema::create('storage_locations', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->text('packaging_instructions')->nullable();
            $table->foreignId('parent_storage_location_id')->nullable()->constrained('storage_locations');
            $table->datetimes();
        });
        Schema::create('material_storage_location', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials');
            $table->foreignId('storage_location_id')->constrained('storage_locations');
            $table->tinyInteger('material_status');
            $table->unsignedInteger('stock')->nullable();
            $table->text('remarks')->nullable();
            $table->datetimes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_storage_location');
        Schema::dropIfExists('storage_locations');
    }
};
