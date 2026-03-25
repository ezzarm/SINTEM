<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\KalenderController;
use App\Http\Controllers\TemuanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PengumumanController as AdminPengumumanController;

// ── PUBLIC ─────────────────────────────────────────────────
Route::get('/', function () { return view('welcome'); });

Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

// ── USER PANEL ─────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', fn() => redirect()->route('pengumuman.index'))->name('dashboard');

    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');

    Route::get('/kalender', [KalenderController::class, 'index'])->name('kalender.index');

    Route::get('/temuan', [TemuanController::class, 'index'])->name('temuan.index');

    Route::get('/laporan/buat',  [LaporanController::class, 'create'])->name('laporan.buat');
    Route::post('/laporan/buat', [LaporanController::class, 'store'])->name('laporan.store');

    Route::get('/laporan/temuan', [LaporanController::class, 'temuan'])->name('laporan.temuan');
    Route::get('/laporan/anonim', [LaporanController::class, 'anonim'])->name('laporan.anonim');

    Route::get('/profile',  [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

});

// ── ADMIN PANEL ────────────────────────────────────────────
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Pengumuman
    Route::get('/pengumuman',                 [AdminPengumumanController::class, 'index'])->name('pengumuman.index');
    Route::post('/pengumuman',                [AdminPengumumanController::class, 'store'])->name('pengumuman.store');
    Route::put('/pengumuman/{id}',            [AdminPengumumanController::class, 'update'])->name('pengumuman.update');
    Route::delete('/pengumuman/{id}',         [AdminPengumumanController::class, 'destroy'])->name('pengumuman.destroy');
    Route::patch('/pengumuman/{id}/toggle',   [AdminPengumumanController::class, 'toggleDraft'])->name('pengumuman.toggleDraft');

    // Kalender (admin)
    Route::get('/kalender', function () { return view('admin.kalender.index'); })->name('kalender.index');

    // Informasi Temuan (admin)
    Route::get('/temuan', function () { return view('admin.temuan.index'); })->name('temuan.index');

    // Laporan Anonim (admin)
    Route::get('/laporan/anonim', function () { return view('admin.laporan.anonim'); })->name('laporan.anonim');

});