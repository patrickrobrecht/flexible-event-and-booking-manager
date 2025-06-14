<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ApiDocumentationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingOptionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentReviewController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventSeriesController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PersonalAccessTokenController;
use App\Http\Controllers\StorageLocationController;
use App\Http\Controllers\SystemInfoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Document;
use App\Models\DocumentReview;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\FormFieldValue;
use App\Models\Location;
use App\Models\Material;
use App\Models\Organization;
use App\Models\PersonalAccessToken;
use App\Models\StorageLocation;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(static function () {
    Route::model('bookings', Booking::class);
    Route::resource('bookings', BookingController::class)
        ->only(['show', 'edit', 'update'])
        ->withTrashed();
    Route::delete('/bookings/{booking}', [BookingController::class, 'delete'])
        ->name('bookings.delete');
    Route::patch('/bookings/{booking}/restore', [BookingController::class, 'restore'])
        ->name('bookings.restore')
        ->withTrashed();
    Route::get('bookings/{booking}/pdf', [BookingController::class, 'showPdf'])
        ->name('bookings.show-pdf');
    Route::model('form_field_value', FormFieldValue::class);
    Route::get('bookings/{booking}/file/{form_field_value}', [BookingController::class, 'downloadFile'])
        ->name('bookings.show-file');

    Route::model('document', Document::class);
    Route::resource('documents', DocumentController::class)
        ->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::prefix('documents/{document}')->group(function () {
        Route::get('download', [DocumentController::class, 'download'])
            ->name('documents.download');
        Route::get('stream', [DocumentController::class, 'stream'])
            ->name('documents.stream');

        Route::model('review', DocumentReview::class);
        Route::resource('reviews', DocumentReviewController::class)
            ->only(['store', 'update']);
    });

    Route::model('event', Event::class);
    Route::resource('events', EventController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::prefix('events/{event:slug}')->group(function () {
        Route::resource('{booking_option:slug}/bookings', BookingController::class)
            ->only(['index']);

        Route::get('{booking_option:slug}/payments', [BookingController::class, 'indexPayments'])
            ->name('bookings.index.payments');
        Route::put('{booking_option:slug}/payments', [BookingController::class, 'updatePayments'])
            ->name('bookings.update.payments');

        Route::resource('booking-options', BookingOptionController::class)
            ->only(['show', 'create', 'store', 'edit', 'update']);

        Route::resource('groups', GroupController::class)
            ->only(['index']);
        Route::post('groups/generate', [GroupController::class, 'generate'])
            ->name('groups.generate');
        Route::delete('groups', [GroupController::class, 'destroyAll'])
            ->name('groups.deleteAll');

        Route::post('documents', [DocumentController::class, 'storeForEvent'])
            ->name('events.documents.store');
    });

    Route::model('event_series', EventSeries::class);
    Route::resource('event-series', EventSeriesController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::prefix('event-series/{event_series:slug}')->group(function () {
        Route::post('documents', [DocumentController::class, 'storeForEventSeries'])
            ->name('event-series.documents.store');
    });

    Route::model('material', Material::class);
    Route::get('materials/search', [MaterialController::class, 'search'])
        ->name('materials.search');
    Route::resource('materials', MaterialController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::model('location', Location::class);
    Route::resource('locations', LocationController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::model('organization', Organization::class);
    Route::resource('organizations', OrganizationController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::prefix('organizations/{organization:slug}')->group(function () {
        Route::post('documents', [DocumentController::class, 'storeForOrganization'])
            ->name('organizations.documents.store');
    });

    Route::model('personal_access_token', PersonalAccessToken::class);
    Route::resource('personal-access-tokens', PersonalAccessTokenController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::model('storage_location', StorageLocation::class);
    Route::resource('storage-locations', StorageLocationController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::model('user', User::class);
    Route::resource('users', UserController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::model('user_role', UserRole::class);
    Route::resource('user-roles', UserRoleController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // My Account
    Route::get('account', [AccountController::class, 'show'])
        ->name('account.show');
    Route::get('account/abilities', [AccountController::class, 'showAbilities'])
        ->name('account.show.abilities');
    Route::get('account/edit', [AccountController::class, 'edit'])
        ->name('account.edit');
    Route::put('account', [AccountController::class, 'update'])
        ->name('account.update');

    // API Documentation
    Route::get('api-docs', [ApiDocumentationController::class, 'index'])
        ->name('api-docs.index');
    Route::get('api-docs/spec', [ApiDocumentationController::class, 'spec'])
        ->name('api-docs.spec');

    // System Information
    Route::get('/system-info', [SystemInfoController::class, 'index'])
        ->name('system-info.index');
});

// Pages that can be public
Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::resource('events', EventController::class)
    ->only(['show']);
Route::resource('event-series', EventSeriesController::class)
    ->only(['show']);

Route::model('booking_option', BookingOption::class);
Route::resource('events/{event:slug}/booking-options', BookingOptionController::class)
    ->only(['show']);

Route::resource('events/{event:slug}/{booking_option:slug}/bookings', BookingController::class)
    ->only(['store']);

require __DIR__ . '/auth.php';
