<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('form_fields', static function (Blueprint $table) {
            $table->foreignId('booking_option_id')->after('id')->nullable()->constrained('booking_options');
        });

        $bookingOptions = DB::table('booking_options')
                            ->where('event_id', '=', 1)
                            ->orderBy('id')
                            ->get();
        foreach ($bookingOptions as $bookingOption) {
            $bookingOption = (array) $bookingOption;

            if (isset($bookingOption['form_id'])) {
                $formFieldGroups = DB::table('form_field_groups')
                                     ->where('form_id', $bookingOption['form_id'])
                                     ->get();
                foreach ($formFieldGroups as $index => $formFieldGroup) {
                    $formFieldGroup = (array) $formFieldGroup;
                    $sort = $index * 100;

                    // Update existing form fields.
                    $formFields = DB::table('form_fields')
                                    ->where('form_field_group_id', $formFieldGroup['id'])
                                    ->get();
                    foreach ($formFields as $formField) {
                        $formField = (array) $formField;
                        $formField['booking_option_id'] = $bookingOption['id'];
                        $formField['sort'] += $sort;

                        DB::table('form_fields')
                          ->where('id', $formField['id'])
                          ->update($formField);
                    }

                    // Convert groups to form fields.
                    DB::table('form_fields')->insert([
                        'booking_option_id' => $bookingOption['id'],
                        'sort' => $sort,
                        'name' => $formFieldGroup['name'],
                        'hint' => $formFieldGroup['description'] ?? null,
                        'container_class' => 'col-12',
                        'type' => 'headline',
                        'required' => true,
                        'editable_after_submission' => false,
                        'form_field_group_id' => $formFieldGroup['id'],
                    ]);
                }
            }
        }

        Schema::table('form_fields', static function (Blueprint $table) {
            $table->foreignId('booking_option_id')->nullable(false)->change();
            $table->dropConstrainedForeignId('form_field_group_id');
        });
        Schema::drop('form_field_groups');

        Schema::table('booking_options', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('form_id');
        });
        Schema::drop('forms');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
