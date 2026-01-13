<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\EnclosureController;
use App\Http\Middleware\EnsureAcceptsJson;
use App\Http\Middleware\EnsureContentTypeJson;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', EnsureAcceptsJson::class, EnsureContentTypeJson::class])
    ->prefix('v1')
    ->group(function () {

        Route::prefix('enclosures')->group(function () {
            Route::post('/', [EnclosureController::class, 'store']);
        });

        Route::prefix('animals')->group(function () {
            Route::post('/', [AnimalController::class, 'store']);
            Route::post('/{animal_id}/transfer', [AnimalController::class, 'transfer']);
        });
    });
