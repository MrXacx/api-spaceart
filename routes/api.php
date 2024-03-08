<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AgreementController;


Route::get('/auth', [AuthController::class, 'authenticate'])->name('auth');

Route::apiResource('/user', UserController::class);
Route::prefix('/user')->name('user.alt.')->group(function() {
   Route::get('/update', fn(Request $request) => redirect()->route('user.update', $request->all()))->name('update');
   Route::get('/delete', fn(Request $request) => redirect()->route('user.destroy', $request->all()))->name('destroy');
});

Route::apiResource('/agreement', AgreementController::class);