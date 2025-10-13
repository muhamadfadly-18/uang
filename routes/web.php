<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TargetController;
use Intervention\Image\Laravel\Facades\Image;

// Login / Logout
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Semua route butuh login
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ==============================
    // ADMIN
    // ==============================
    Route::middleware('role:admin')->group(function () {

        // Pemasukan
        Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan.index');
        Route::get('/pemasukan/create', [PemasukanController::class, 'create'])->name('pemasukan.create');
        Route::post('/pemasukan', [PemasukanController::class, 'store'])->name('pemasukan.store');
        Route::get('/pemasukan/{id}/edit', [PemasukanController::class, 'edit'])->name('pemasukan.edit');
        Route::put('/pemasukan/{id}', [PemasukanController::class, 'update'])->name('pemasukan.update'); // ✅ PUT
        Route::delete('/pemasukan/{id}', [PemasukanController::class, 'destroy'])->name('pemasukan.destroy');

        // Pengeluaran
        Route::get('/pengeluaranday', [PengeluaranController::class, 'index'])->name('pengeluaranday.index');
        Route::get('/pengeluaranday/create', [PengeluaranController::class, 'create'])->name('pengeluaranday.create');
        Route::post('/pengeluaranday', [PengeluaranController::class, 'store'])->name('pengeluaranday.store');
        Route::get('/pengeluaranday/{id}/edit', [PengeluaranController::class, 'edit'])->name('pengeluaranday.edit');
        Route::put('/pengeluaranday/{id}', [PengeluaranController::class, 'update'])->name('pengeluaranday.update'); // ✅ PUT
        Route::delete('/pengeluaranday/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaranday.destroy');

        // Scan struk
        Route::post('/pengeluaran/scan', [PengeluaranController::class, 'scan'])->name('pengeluaran.scan');

        // Target
        Route::get('/target', [TargetController::class, 'index'])->name('target.index');
        Route::get('/target/create', [TargetController::class, 'create'])->name('target.create');
        Route::post('/target', [TargetController::class, 'store'])->name('target.store');
        Route::get('/target/{id}/edit', [TargetController::class, 'edit'])->name('target.edit');
        Route::put('/target/{id}', [TargetController::class, 'update'])->name('target.update'); // ✅ PUT
        Route::delete('/target/{id}', [TargetController::class, 'destroy'])->name('target.destroy');
        Route::post('/target/{id}/tercapai', [TargetController::class, 'updateTercapai'])->name('target.tercapai');

        // History
        Route::get('/history', [HistoryController::class, 'index'])->name('history');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update'); // ✅ PUT
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // ==============================
    // USER
    // ==============================
    Route::middleware('role:user')->group(function () {

        // Pemasukan user
        Route::get('/pemasukan-saya', [PemasukanController::class, 'userIndex'])->name('pemasukan.user.index');
        Route::get('/pemasukan-saya/create', [PemasukanController::class, 'create'])->name('pemasukan.user.create');
        Route::post('/pemasukan-saya', [PemasukanController::class, 'store'])->name('pemasukan.user.store');
        Route::get('/pemasukan-saya/{id}/edit', [PemasukanController::class, 'edit'])->name('pemasukan.user.edit');
        Route::put('/pemasukan-saya/{id}', [PemasukanController::class, 'update'])->name('pemasukan.user.update'); // ✅ PUT
        Route::delete('/pemasukan-saya/{id}', [PemasukanController::class, 'destroy'])->name('pemasukan.user.destroy');

        // Pengeluaran user
        Route::get('/pengeluaran-saya', [PengeluaranController::class, 'userIndex'])->name('pengeluaranday.user.index');
        Route::get('/pengeluaran-saya/create', [PengeluaranController::class, 'create'])->name('pengeluaranday.user.create');
        Route::post('/pengeluaran-saya', [PengeluaranController::class, 'store'])->name('pengeluaranday.user.store');
        Route::get('/pengeluaran-saya/{id}/edit', [PengeluaranController::class, 'edit'])->name('pengeluaranday.user.edit');
        Route::put('/pengeluaran-saya/{id}', [PengeluaranController::class, 'update'])->name('pengeluaranday.user.update'); // ✅ PUT
        Route::delete('/pengeluaran-saya/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaranday.user.destroy');

        // Target user
        Route::get('/target-saya', [TargetController::class, 'userIndex'])->name('target.user.index');
        Route::get('/target-saya/create', [TargetController::class, 'create'])->name('target.user.create');
        Route::post('/target-saya', [TargetController::class, 'store'])->name('target.user.store');
        Route::get('/target-saya/{id}/edit', [TargetController::class, 'edit'])->name('target.user.edit');
        Route::put('/target-saya/{id}', [TargetController::class, 'update'])->name('target.user.update'); // ✅ PUT
        Route::delete('/target-saya/{id}', [TargetController::class, 'destroy'])->name('target.user.destroy');
        Route::post('/target-saya/{id}/tercapai', [TargetController::class, 'updateTercapai'])->name('target.user.tercapai');

        // History user
        Route::get('/history-saya', [HistoryController::class, 'userHistory'])->name('history.user');
    });
});

// Debug route untuk cek role
Route::get('/cek-role', function () {
    if (Auth::check()) {
        return response()->json([
            'user' => Auth::user()->name,
            'role' => Auth::user()->role,
        ]);
    } else {
        return response()->json(['message' => 'Belum login.']);
    }
});
