<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Alter table for {@see Booking}s.
     */
    public function up(): void
    {
        Schema::table('bookings', static function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('country');
            $table->softDeletesDatetime();
        });

        Schema::table('users', static function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', static function (Blueprint $table) {
            $table->dropColumn('date_of_birth');
            $table->dropSoftDeletes();
        });

        Schema::table('users', static function (Blueprint $table) {
            $table->dropColumn('date_of_birth');
        });
    }
};
