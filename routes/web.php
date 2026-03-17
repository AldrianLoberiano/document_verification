<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/index.html');

Route::get('/admin', function () {
    return response()->file(public_path('admin.html'));
});

Route::get('/verify', function () {
    return response()->file(public_path('verify.html'));
});

Route::get('/verify/{code}', function () {
    return response()->file(public_path('verify.html'));
});
