<?php

use App\Enums\BookingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Alter {@see App\Models\Booking}s table.
     */
    public function up(): void
    {
        Schema::table('booking_options', static function (Blueprint $table) {
            $table->unsignedInteger('waiting_list_places')->default(0)->nullable();
            $table->text('waiting_list_text')->nullable();
        });

        Schema::table('bookings', static function (Blueprint $table) {
            $table->unsignedInteger('status')->default(BookingStatus::Confirmed->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_options', static function (Blueprint $table) {
            $table->dropColumn('waiting_list_places');
            $table->dropColumn('waiting_list_text');
        });

        Schema::table('bookings', static function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
