<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::get('/me', function (Request $request) {
            return $request->user();
        });

        Route::prefix('files')->group(function () {
            Route::post('/', [FileController::class, 'store']);
            Route::get('/history', [FileController::class, 'history']);
            Route::get('/{file}/content', [FileController::class, 'content']);
        });
    });
});
