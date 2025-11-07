<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Alter {@see App\Models\BookingOption}s table.
     */
    public function up(): void
    {
        Schema::table('booking_options', static function (Blueprint $table) {
            $table->unsignedInteger('payment_due_days')->nullable()->after('price_conditions');
            $table->text('confirmation_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_options', static function (Blueprint $table) {
            $table->dropColumn('payment_due_days');
            $table->dropColumn('confirmation_text');
        });
    }
};
