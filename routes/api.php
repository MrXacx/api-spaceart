<?php

declare(strict_types=1);

use App\Http\Controllers\AgreementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\SelectiveCandidateController;
use App\Http\Controllers\SelectiveController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;

Route::get('/auth', [AuthController::class, 'authenticate'])->name('auth');
Route::middleware('auth:sanctum')->get('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

$apiControllers = [
    'user' => UserController::class,
    'agreement' => AgreementController::class,
    'selective' => SelectiveController::class,
];
foreach ($apiControllers as $route => $class) {
    Route::apiResource("/$route", $class, ['parameters' => [$route => 'id']]);
    Route::prefix("/$route")->name('user.alt.')->group(function () use ($route) {
        Route::get('/update/{id}', fn (Request $request) => redirect()->route("$route.update", $request->all()))->name('update');
        Route::get('/delete/{id}', fn (Request $request) => redirect()->route("$route.'.destroy", $request->all()))->name('destroy');
    });
}

Route::apiResource('/post', PostController::class, ['except' => ['update']]);

Route::apiResource(
    '/agreement/{agreement}/rate',
    RateController::class,
    [
        'parameters' => ['rate' => 'author'],
        'except' => ['index'],
    ]
);

Route::post('/selective/{selective}/candidate', [SelectiveCandidateController::class, 'store']);
