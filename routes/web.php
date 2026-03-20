<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\KalenderController;
use App\Http\Controllers\TemuanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;

// PUBLIC ROUTES
Route::get('/', function () {
    return view('welcome'); // landing page
});

Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

// AUTHENTICATED ROUTES
Route::group([], function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard 
    Route::get('/dashboard', function () {
        return redirect()->route('pengumuman.index');
    })->name('dashboard');

    // MAIN

    // Pengumuman
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    Route::get('/pengumuman/{id}', [PengumumanController::class, 'show'])->name('pengumuman.show');

    // Kalender
    Route::get('/kalender', [KalenderController::class, 'index'])->name('kalender.index');

    // Informasi Temuan
    Route::get('/temuan', [TemuanController::class, 'index'])->name('temuan.index');
    Route::get('/temuan/{id}', [TemuanController::class, 'show'])->name('temuan.show');

    // Buat Laporan
    Route::get('/laporan/buat',  [LaporanController::class, 'create'])->name('laporan.buat');
    Route::post('/laporan/buat', [LaporanController::class, 'store'])->name('laporan.store');

    //OTHER

    // Manajemen Laporan → Laporan Temuan
    Route::get('/laporan/temuan', [LaporanController::class, 'temuan'])->name('laporan.temuan');

    // Manajemen Laporan → Laporan Anonim
    Route::get('/laporan/anonim', [LaporanController::class, 'anonim'])->name('laporan.anonim');

    // Profil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

});