<?php

use Illuminate\Support\Facades\Route;

$page = static function (string $path) {
    return response()->file(resource_path("views/html/{$path}.html"));
};

Route::get('/', static fn() => $page('index'));

Route::get('/admin', static fn() => $page('admin/index'));
Route::get('/admin/login', static fn() => $page('admin/login'));
Route::get('/admin/dashboard', static fn() => $page('admin/dashboard'));

Route::get('/verify', static fn() => $page('verify/index'));
Route::get('/verify/check', static fn() => $page('verify/check'));
Route::get('/verify/{code}', static fn() => $page('verify/check'))
    ->where('code', '^(?!check$).+');

// Backward-compatible legacy URLs without redirect chains.
Route::get('/index.html', static fn() => $page('index'));
Route::get('/admin/index.html', static fn() => $page('admin/index'));
Route::get('/admin/login.html', static fn() => $page('admin/login'));
Route::get('/admin/dashboard.html', static fn() => $page('admin/dashboard'));
Route::get('/verify/index.html', static fn() => $page('verify/index'));
Route::get('/verify/check.html', static fn() => $page('verify/check'));

Route::redirect('/login', '/admin/login');
Route::redirect('/check', '/verify/check');
Route::redirect('/home', '/');
Route::redirect('/homepage', '/');
