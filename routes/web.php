<?php

declare(strict_types=1);

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('user')
    ->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'list'])->name('list');
        Route::post('/', [UserController::class, 'store'])->name('store');

        Route::name('show.')->group(function () {
            Route::get('/{id}/{type?}', [UserController::class, 'show'])
                ->whereNumber('id')
                ->name('public');

            Route::get('/{token}/{type?}', [UserController::class, 'show'])
                ->whereUuid('token')
                ->name('private');
        });

        Route::prefix('/{token}')->group(function () {
            Route::put('/', [UserController::class, 'update'])->name('update');
            Route::delete('/', [UserController::class, 'destroy'])->name('delete');
        })->whereUuid('token');

        Route::name('alt')->group(function () {
            Route::get('/update/{token}', fn ($token) => redirect()->route('user.update',
                ['token' => $token],
                headers: [
                    'method' => 'PUT',
                ]))
                ->whereUuid('token')
                ->name('update.alt');

            Route::get('/delete/{token}', fn ($token) => redirect()->route('user.delete'))
                ->whereUuid('token')
                ->name('delete.alt');
        });
    });
