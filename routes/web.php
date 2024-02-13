<?php

declare(strict_types=1);

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('user')
    ->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'list'])->name('list'); // GET: host/user
        Route::post('/', [UserController::class, 'store'])->name('store'); // POST: host/user
    
        Route::prefix('/{id}')->group(function () {
            Route::get('/{type?}', [UserController::class, 'show'])->name('show'); // GET: host/user/{id}/{type?}    
            Route::put('/', [UserController::class, 'update'])->name('update');  // PUT: host/user/{token}
            Route::delete('/', [UserController::class, 'destroy'])->name('delete'); // DELETE: host/user/{token}
        })->whereNumber('id');

        Route::name('alias.')->group(function () {
            Route::get('/update/{id}', fn($token) => redirect()->route( // GET: host/user/update/{token}
                'user.update',
                parameters: ['id' => $id],
                headers: ['method' => 'PUT']
            ))
                ->whereNumber('id')
                ->name('update');

            Route::get('/delete/{id}', fn($id) => redirect()->route(
                'user.delete',
                parameters: ['id' => $id],
                headers: ['method' => 'PUT']
            )) // GET: host/user/update/{token}
                ->whereNumber('id')
                ->name('delete');
        });
    });
