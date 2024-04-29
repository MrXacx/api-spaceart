<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AgreementController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\SelectiveController;

Route::get('/auth', [AuthController::class, 'authenticate'])->name('auth');


$apiControllers = [
    'user' => UserController::class,
    'agreement' => AgreementController::class,
    'selective' => SelectiveController::class,
];

foreach ($apiControllers as $route => $class) {

    Route::apiResource("/$route", $class, ['parameters' => [$route => 'id']]);
    Route::prefix("/$route")->name('user.alt.')->group(function () {
        Route::get('/update/{id}', fn(Request $request) => redirect()->route($route . '.update', $request->all()))->name('update');
        Route::get('/delete/{id}', fn(Request $request) => redirect()->route($route . '.destroy', $request->all()))->name('destroy');
    });

}

Route::apiResource('/agreement/{agreement}/rate', RateController::class, ['parameters' => ['rate' => 'author']]);
