<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Create the table for {@see \App\Models\EventSeries}s.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('event_series', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('visibility');
            $table->foreignId('parent_event_series_id')->nullable()->constrained('event_series');
            $table->timestamps();
        });
        Schema::table('events', static function (Blueprint $table) {
            $table->foreignId('event_series_id')->after('parent_event_id')->nullable()->constrained('event_series');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('events', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_series_id');
        });
        Schema::dropIfExists('event_series');
    }
};
