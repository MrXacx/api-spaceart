<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


Route::get('/auth', [AuthController::class, 'authenticate'])->name('auth');
Route::resource('/user', UserController::class, [
    'except' => ['edit']
]);
Route::prefix('/user')->name('user.alt.')->group(function() {
   Route::get('/update', fn(Request $request) => redirect()->route('user.update', $request->all()))->name('update');
   Route::get('/delete', fn(Request $request) => redirect()->route('user.destroy', $request->all()))->name('destroy');
});