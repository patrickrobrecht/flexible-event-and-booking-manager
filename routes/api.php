<?php

use App\Enums\Ability;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\EventSeriesApiController;
use App\Http\Controllers\Api\LocationApiController;
use App\Http\Controllers\Api\OrganizationApiController;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'api.'], static function () {
    Route::apiResource('event-series', EventSeriesApiController::class)
        ->only(['index', 'show'])
        ->middleware([
            'abilities:' . Ability::ViewEventSeries->value,
        ]);

    Route::apiResource('events', EventApiController::class)
        ->only(['index', 'show'])
        ->middleware([
            'abilities:' . Ability::ViewEvents->value,
        ]);

    Route::apiResource('locations', LocationApiController::class)
        ->only(['index', 'show'])
        ->middleware([
            'abilities:' . Ability::ViewLocations->value,
        ]);

    Route::apiResource('organizations', OrganizationApiController::class)
        ->only(['index', 'show'])
        ->middleware([
            'abilities:' . Ability::ViewOrganizations->value,
        ]);
});
