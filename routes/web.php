<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingOptionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventSeriesController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PersonalAccessTokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Form;
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
         ->only(['show', 'edit', 'update']);

    Route::resource('events', EventController::class)
         ->only(['index', 'create', 'store', 'edit', 'update']);
    Route::resource('events/{event:slug}/booking-options', BookingOptionController::class)
         ->only(['show', 'create', 'store', 'edit', 'update']);
    Route::resource('events/{event:slug}/{booking_option:slug}/bookings', BookingController::class)
         ->only(['index']);

    Route::model('event_series', EventSeries::class);
    Route::resource('event-series', EventSeriesController::class)
         ->only(['index', 'show', 'create', 'store', 'edit', 'update']);

    Route::model('form', Form::class);
    Route::resource('forms', FormController::class)
        ->only(['index', 'show', 'create', 'store', 'edit', 'update']);

    Route::model('location', Location::class);
    Route::resource('locations', LocationController::class)
         ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::model('organization', Organization::class);
    Route::resource('organizations', OrganizationController::class)
         ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::model('personal_access_token', PersonalAccessToken::class);
    Route::resource('personal-access-tokens', PersonalAccessTokenController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::model('user', User::class);
    Route::resource('users', UserController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::model('user_role', UserRole::class);
    Route::resource('user-roles', UserRoleController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);

    // My Account
    Route::get('account', [AccountController::class, 'edit'])
        ->name('account.edit');
    Route::put('account', [AccountController::class, 'update'])
        ->name('account.update');
});

Route::get('/', [DashboardController::class, 'index'])
     ->name('dashboard');

Route::model('event', Event::class);
Route::resource('events', EventController::class)
     ->only(['show']);

Route::model('booking_option', BookingOption::class);
Route::resource('events/{event:slug}/booking-options', BookingOptionController::class)
     ->only(['show']);

Route::resource('events/{event:slug}/{booking_option:slug}/bookings', BookingController::class)
     ->only(['store']);

require __DIR__ . '/auth.php';
