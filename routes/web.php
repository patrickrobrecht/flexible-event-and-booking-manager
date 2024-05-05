<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingOptionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventSeriesController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PersonalAccessTokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\FormFieldValue;
use App\Models\Location;
use App\Models\Organization;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

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
    });

    Route::model('event', Event::class);
    Route::resource('events', EventController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);
    Route::prefix('events/{event:slug}')->group(function () {
        Route::resource('{booking_option:slug}/bookings', BookingController::class)
            ->only(['index']);
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
        ->only(['index', 'create', 'store', 'edit', 'update']);
    Route::prefix('event-series/{event_series:slug}')->group(function () {
        Route::post('documents', [DocumentController::class, 'storeForEventSeries'])
            ->name('event-series.documents.store');
    });

    Route::model('location', Location::class);
    Route::resource('locations', LocationController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::model('organization', Organization::class);
    Route::resource('organizations', OrganizationController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);
    Route::prefix('organizations/{organization}')->group(function () {
        Route::post('documents', [DocumentController::class, 'storeForOrganization'])
            ->name('organizations.documents.store');
    });

    Route::model('personal_access_token', PersonalAccessToken::class);
    Route::resource('personal-access-tokens', PersonalAccessTokenController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::model('user', User::class);
    Route::resource('users', UserController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update']);

    Route::model('user_role', UserRole::class);
    Route::resource('user-roles', UserRoleController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update']);

    // My Account
    Route::get('account', [AccountController::class, 'show'])
        ->name('account.show');
    Route::get('account/edit', [AccountController::class, 'edit'])
        ->name('account.edit');
    Route::put('account', [AccountController::class, 'update'])
        ->name('account.update');
});

// Pages that can be public
Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::resource('events', EventController::class)
    ->only(['show']);
Route::resource('event-series', EventSeriesController::class)
    ->only(['show']);
Route::resource('organizations', OrganizationController::class)
    ->only(['show']);

Route::model('booking_option', BookingOption::class);
Route::resource('events/{event:slug}/booking-options', BookingOptionController::class)
    ->only(['show']);

Route::resource('events/{event:slug}/{booking_option:slug}/bookings', BookingController::class)
    ->only(['store']);

require __DIR__ . '/auth.php';
