<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/index.html');

Route::get('/admin', function () {
    return response()->file(public_path('admin-home.html'));
});

Route::get('/admin/login', function () {
    return response()->file(public_path('admin-login.html'));
});

Route::get('/admin/dashboard', function () {
    return response()->file(public_path('admin-dashboard.html'));
});

Route::get('/verify', function () {
    return response()->file(public_path('verify-home.html'));
});

Route::get('/verify/check', function () {
    return response()->file(public_path('verify.html'));
});

Route::get('/verify/{code}', function () {
    return response()->file(public_path('verify.html'));
});
