<?php

use App\Options\Ability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/account', static function (Request $request) {
    return $request->user();
})->middleware('ability:' . Ability::ViewAccount->value);
