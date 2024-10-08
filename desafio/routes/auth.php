<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::post('login', [LoginController::class, 'authenticate'])->name('auth.login');
Route::post('logout', [LoginController::class, 'logout'])->name('auth.logout');
