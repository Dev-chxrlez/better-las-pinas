<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/{path?}', function () {
    $spa = public_path('index.html');

    return File::exists($spa)
        ? response()->file($spa)
        : view('welcome');
})->where('path', '^(?!api(?:/|$)|sanctum(?:/|$)|up$).*$');
