<?php

use App\Http\Controllers\GuruController;
use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TahunAjaranController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::resource('siswa', SiswaController::class)->except(['create', 'show', 'edit']);
    Route::resource('guru', GuruController::class)->except(['create', 'show', 'edit']);
    Route::resource('kelas', KelasController::class)->except(['create', 'show', 'edit']);
    Route::resource('tahun-ajaran', TahunAjaranController::class)->except(['create', 'show', 'edit']);
    Route::resource('jadwal-pelajaran', JadwalPelajaranController::class)->except(['create', 'show', 'edit']);
    Route::resource('kehadiran', KehadiranController::class)->except(['show']);
});

require __DIR__.'/settings.php';
