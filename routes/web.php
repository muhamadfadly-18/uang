<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Intervention\Image\Laravel\Facades\Image;

// Login / Logout
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Semua route harus login
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Hanya admin bisa lihat & kelola semua data
    Route::middleware('role:admin')->group(function () {

        // Pemasukan
        Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan.index');
        Route::get('/pemasukan/create', [PemasukanController::class, 'create'])->name('pemasukan.create');
        Route::post('/pemasukan', [PemasukanController::class, 'store'])->name('pemasukan.store');
        Route::get('/pemasukan/{id}/edit', [PemasukanController::class, 'edit'])->name('pemasukan.edit');
        Route::post('/pemasukan/{id}', [PemasukanController::class, 'update'])->name('pemasukan.update');
        Route::delete('/pemasukan/{id}', [PemasukanController::class, 'destroy'])->name('pemasukan.destroy');

        // Pengeluaran
        Route::get('/pengeluaranday', [PengeluaranController::class, 'index'])->name('pengeluaranday.index');
        Route::get('/pengeluaranday/create', [PengeluaranController::class, 'create'])->name('pengeluaranday.create');
        Route::post('/pengeluaranday', [PengeluaranController::class, 'store'])->name('pengeluaranday.store');
        Route::get('/pengeluaranday/{id}/edit', [PengeluaranController::class, 'edit'])->name('pengeluaranday.edit');
        Route::post('/pengeluaranday/{id}', [PengeluaranController::class, 'update'])->name('pengeluaranday.update');
        Route::delete('/pengeluaranday/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaranday.destroy');

        // Scan struk
        Route::post('/pengeluaran/scan', [PengeluaranController::class, 'scan'])->name('pengeluaran.scan');

        // History
        Route::get('/history', [HistoryController::class, 'index'])->name('history');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::post('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // User hanya bisa lihat datanya sendiri
    Route::middleware('role:user')->group(function () {
        Route::get('/pemasukan-saya', [PemasukanController::class, 'userIndex'])->name('pemasukan.user');
         Route::get('/pemasukan/create', [PemasukanController::class, 'create'])->name('pemasukan.create');
        Route::post('/pemasukan', [PemasukanController::class, 'store'])->name('pemasukan.store');
        Route::get('/pemasukan/{id}/edit', [PemasukanController::class, 'edit'])->name('pemasukan.edit');
        Route::post('/pemasukan/{id}', [PemasukanController::class, 'update'])->name('pemasukan.update');
        Route::delete('/pemasukan/{id}', [PemasukanController::class, 'destroy'])->name('pemasukan.destroy');



        Route::get('/pengeluaran-saya', [PengeluaranController::class, 'userIndex'])->name('pengeluaranday.user');
        Route::get('/pengeluaranday/create', [PengeluaranController::class, 'create'])->name('pengeluaranday.create');
        Route::post('/pengeluaranday', [PengeluaranController::class, 'store'])->name('pengeluaranday.store');
        Route::get('/pengeluaranday/{id}/edit', [PengeluaranController::class, 'edit'])->name('pengeluaranday.edit');
        Route::post('/pengeluaranday/{id}', [PengeluaranController::class, 'update'])->name('pengeluaranday.update');
        Route::delete('/pengeluaranday/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaranday.destroy');


        Route::get('/history-saya', [HistoryController::class, 'userHistory'])->name('history.user');
    });
});


// ✅ ini versi image-laravel, bukan yang lama

Route::get('/test-image', function () {
    // Buat image kosong 300x200 dengan background hijau
    $image = Image::create(300, 200)
        ->fill('#4CAF50');

    // Tambahkan teks di tengah
    $image->text('Hello Fadly!', 150, 100, function ($font) {
        // optional: font custom
        // $font->filename(public_path('fonts/arial.ttf'));
        $font->size(24);
        $font->color('#ffffff');
        $font->align('center');
        $font->valign('middle');
    });

    // Simpan ke public
    $path = public_path('test-image.png');
    $image->save($path);

    return '✅ Gambar berhasil dibuat di: <code>' . $path . '</code>';
});


Route::get('/scan-struk', function() {
    return view('pengeluaran.scan'); // nanti kita buat view form upload
});

Route::post('/scan-struk', [PengeluaranController::class, 'scan'])->name('pengeluaran.scan');