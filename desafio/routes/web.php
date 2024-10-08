<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

include __DIR__.'/auth.php';
