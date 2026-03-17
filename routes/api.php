<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\VerificationController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::get('/verify/{code}', [VerificationController::class, 'verify']);

Route::middleware('api.token')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('/admin')->middleware('role:admin')->group(function () {
        Route::get('/documents', [DocumentController::class, 'index']);
        Route::post('/documents', [DocumentController::class, 'store']);
        Route::get('/documents/{document}', [DocumentController::class, 'show']);
        Route::post('/documents/{document}', [DocumentController::class, 'update']);
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);
        Route::post('/documents/{document}/revoke', [DocumentController::class, 'revoke']);
        Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
        Route::get('/logs', [DocumentController::class, 'logs']);
    });
});
