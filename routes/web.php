<?php

use App\Http\Controllers\KelasController;
use App\Http\Controllers\TahunAjaranController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::resource('kelas', KelasController::class)->except(['create', 'show', 'edit']);
    Route::resource('tahun-ajaran', TahunAjaranController::class)->except(['create', 'show', 'edit']);
});

require __DIR__.'/settings.php';
