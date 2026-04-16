<?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\PengumumanController;
    use App\Http\Controllers\KalenderController;
    use App\Http\Controllers\TemuanController;
    use App\Http\Controllers\LaporanController;
    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\Admin\PengumumanController as AdminPengumumanController;
    use App\Http\Controllers\Admin\KalenderController  as AdminKalenderController;
    use App\Http\Controllers\Admin\TemuanController    as AdminTemuanController;
    use App\Http\Controllers\Admin\LaporanAnonimController as AdminLaporanAnonimController;

    // ── PUBLIC ─────────────────────────────────────────────────
    Route::get('/', function () { return view('welcome'); });
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', function () { return view('auth.forgot-password'); })->name('password.request');

    // ── USER PANEL ─────────────────────────────────────────────
    Route::middleware(['auth'])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', fn() => redirect()->route('pengumuman.index'))->name('dashboard');

        Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
        Route::get('/kalender',   [KalenderController::class,   'index'])->name('kalender.index');

        Route::get('/temuan',       [TemuanController::class, 'index'])->name('temuan.index');
        Route::get('/temuan/buat',  [TemuanController::class, 'create'])->name('temuan.buat');
        Route::post('/temuan/buat', [TemuanController::class, 'store'])->name('temuan.store');

        Route::get('/laporan/buat',  [LaporanController::class, 'create'])->name('laporan.buat');
        Route::post('/laporan/buat', [LaporanController::class, 'store'])->name('laporan.store');

        Route::get('/laporan/temuan',      [LaporanController::class, 'temuan'])->name('laporan.temuan');
        Route::put('/temuan/{id}',         [LaporanController::class, 'updateTemuan'])->name('laporan.temuan.update');
        Route::delete('/temuan/{id}',      [LaporanController::class, 'destroyTemuan'])->name('laporan.temuan.destroy');

        Route::get('/laporan/anonim',         [LaporanController::class, 'anonim'])->name('laporan.anonim');
        Route::delete('/laporan/anonim/{id}', [LaporanController::class, 'destroyAnonim'])->name('laporan.anonim.destroy');

        Route::get('/profile',          [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    // ── ADMIN PANEL ────────────────────────────────────────────
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/profile',          [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // Pengumuman
        Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
            Route::get('/',              [AdminPengumumanController::class, 'index'])->name('index');
            Route::get('/buat',          [AdminPengumumanController::class, 'create'])->name('buat');
            Route::post('/',             [AdminPengumumanController::class, 'store'])->name('store');
            Route::put('/{id}',          [AdminPengumumanController::class, 'update'])->name('update');
            Route::delete('/{id}',       [AdminPengumumanController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle', [AdminPengumumanController::class, 'toggleDraft'])->name('toggleDraft');
        });

        // Kalender Kegiatan
        Route::prefix('kalender')->name('kalender.')->group(function () {
            Route::get('/',              [AdminKalenderController::class, 'index'])->name('index');
            Route::post('/',             [AdminKalenderController::class, 'store'])->name('store');
            Route::put('/{id}',          [AdminKalenderController::class, 'update'])->name('update');
            Route::delete('/{id}',       [AdminKalenderController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle', [AdminKalenderController::class, 'toggle'])->name('toggle');
        });

        // Informasi Temuan
        Route::prefix('temuan')->name('temuan.')->group(function () {
            Route::get('/',              [AdminTemuanController::class, 'index'])  ->name('index');
            Route::post('/',             [AdminTemuanController::class, 'store'])  ->name('store');
            Route::put('/{id}',          [AdminTemuanController::class, 'update']) ->name('update');
            Route::delete('/{id}',       [AdminTemuanController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/approve',[AdminTemuanController::class, 'approve'])->name('approve');
            Route::patch('/{id}/reject', [AdminTemuanController::class, 'reject']) ->name('reject');
        });

        // Laporan Anonim
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/anonim',              [AdminLaporanAnonimController::class, 'index'])       ->name('anonim');
            Route::patch('/anonim/{id}/status',[AdminLaporanAnonimController::class, 'updateStatus'])->name('anonim.status');
            Route::delete('/anonim/{id}',      [AdminLaporanAnonimController::class, 'destroy'])     ->name('anonim.destroy');
        });
    });

    // ── SUPERADMIN PANEL ───────────────────────────────────────
    Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {

        Route::get('/profile',          [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // ── Accounts CRUD ──
        Route::prefix('accounts')->name('accounts.')->group(function () {
            Route::get('/',         [\App\Http\Controllers\Superadmin\AccountController::class, 'index']) ->name('index');
            Route::get('/create',   [\App\Http\Controllers\Superadmin\AccountController::class, 'create'])->name('create');
            Route::post('/',        [\App\Http\Controllers\Superadmin\AccountController::class, 'store']) ->name('store');
            Route::get('/{id}/edit',[\App\Http\Controllers\Superadmin\AccountController::class, 'edit'])  ->name('edit');
            Route::put('/{id}',     [\App\Http\Controllers\Superadmin\AccountController::class, 'update'])->name('update');
            Route::delete('/{id}',  [\App\Http\Controllers\Superadmin\AccountController::class, 'destroy'])->name('destroy');
        });

        // ── Roles CRUD ──
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/',       [\App\Http\Controllers\Superadmin\RoleController::class, 'index'])  ->name('index');
            Route::post('/',      [\App\Http\Controllers\Superadmin\RoleController::class, 'store'])  ->name('store');
            Route::put('/{id}',   [\App\Http\Controllers\Superadmin\RoleController::class, 'update']) ->name('update');
            Route::delete('/{id}',[\App\Http\Controllers\Superadmin\RoleController::class, 'destroy'])->name('destroy');
        });

        // ── Report Categories ──
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/',       [\App\Http\Controllers\Superadmin\CategoryController::class, 'index'])  ->name('index');
            Route::post('/',      [\App\Http\Controllers\Superadmin\CategoryController::class, 'store'])  ->name('store');
            Route::put('/{id}',   [\App\Http\Controllers\Superadmin\CategoryController::class, 'update']) ->name('update');
            Route::delete('/{id}',[\App\Http\Controllers\Superadmin\CategoryController::class, 'destroy'])->name('destroy');
        });
    });