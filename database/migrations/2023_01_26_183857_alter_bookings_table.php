<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Alter table for {@see \App\Models\Booking}s.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('bookings', static function (Blueprint $table) {
            $table->dateTime('paid_at')->nullable()->after('price');
            $table->text('comment')->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('bookings', static function (Blueprint $table) {
            $table->dropColumn('paid_at');
            $table->dropColumn('comment');
        });
    }
};
