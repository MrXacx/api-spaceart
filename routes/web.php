<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('l5-swagger.default.api'));
