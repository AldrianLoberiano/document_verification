<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/index.html', '/');
Route::redirect('/admin/index.html', '/admin');
Route::redirect('/admin/login.html', '/admin/login');
Route::redirect('/admin/dashboard.html', '/admin/dashboard');
Route::redirect('/verify/index.html', '/verify');
Route::redirect('/verify/check.html', '/verify/check');
Route::redirect('/check', '/verify/check');

Route::get('/', function () {
    return response()->file(resource_path('views/html/index.html'));
});

Route::redirect('/login', '/admin/login');

Route::get('/admin', function () {
    return response()->file(resource_path('views/html/admin/index.html'));
});

Route::get('/admin/login', function () {
    return response()->file(resource_path('views/html/admin/login.html'));
});

Route::get('/admin/dashboard', function () {
    return response()->file(resource_path('views/html/admin/dashboard.html'));
});

Route::get('/verify', function () {
    return response()->file(resource_path('views/html/verify/index.html'));
});

Route::get('/verify/check', function () {
    return response()->file(resource_path('views/html/verify/check.html'));
});

Route::get('/verify/{code}', function () {
    return response()->file(resource_path('views/html/verify/check.html'));
});
