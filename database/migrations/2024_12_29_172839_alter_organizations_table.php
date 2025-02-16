<?php

use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Alter {@see Organization}s table.
     */
    public function up(): void
    {
        Schema::table('organizations', static function (Blueprint $table) {
            $table->dropColumn('representatives');
            $table->string('slug')->nullable()->after('name');
            $table->string('phone')->nullable()->after('register_entry');
            $table->string('email')->nullable()->after('phone');
            $table->string('bank_account_holder')->nullable()->after('website_url');
            $table->string('iban')->nullable()->after('bank_account_holder');
            $table->string('bank_name')->nullable()->after('iban');
        });

        Organization::query()
            ->update([
                'slug' => DB::raw('id'),
                'bank_account_holder' => env('BANK_ACCOUNT_HOLDER'),
                'iban' => env('BANK_ACCOUNT_IBAN'),
                'bank_name' => env('BANK_ACCOUNT_BANK_NAME'),
            ]);

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
            $table->dropColumn('phone');
            $table->dropColumn('email');
            $table->dropColumn('bank_account_holder');
            $table->dropColumn('iban');
            $table->dropColumn('bank_name');
        });
    }
};
