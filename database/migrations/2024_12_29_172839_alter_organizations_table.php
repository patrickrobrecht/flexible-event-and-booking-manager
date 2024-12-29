<?php

use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Alter {@see \App\Models\Organization}s table.
     */
    public function up(): void
    {
        Schema::table('organizations', static function (Blueprint $table) {
            $table->dropColumn('representatives');
            $table->string('slug')->after('name')->nullable();
        });

        Organization::query()->update(['slug' => DB::raw('id')]);

        Schema::table('organizations', static function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', static function (Blueprint $table) {
            $table->string('representatives')->nullable();
            $table->dropColumn('slug');
        });
    }
};
