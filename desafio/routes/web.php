<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');
Route::view('/arquivos', 'files')->name('files');

include __DIR__.'/auth.php';
