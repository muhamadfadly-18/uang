<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthApiController;
use App\Http\Controllers\api\PemasukanApiController;
use App\Http\Controllers\api\PengeluaranApiController;
use App\Http\Controllers\api\TargetApiController;
use App\Http\Controllers\api\HistoryApiController;
use App\Http\Controllers\api\ProfileApiController;

// ========================
// AUTH
// ========================
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/register', [AuthApiController::class, 'register']);

// Semua route berikut butuh login dengan Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // ========================
    // PROFILE
    // ========================
    Route::get('/profile', [ProfileApiController::class, 'show']);
    Route::put('/profile', [ProfileApiController::class, 'update']);
    Route::post('/logout', [AuthApiController::class, 'logout']);

    // ========================
    // PEMASUKAN
    // ========================
    Route::get('/pemasukan', [PemasukanApiController::class, 'index']);
    Route::post('/pemasukan', [PemasukanApiController::class, 'store']);
    Route::get('/pemasukan/{id}', [PemasukanApiController::class, 'show']);
    Route::put('/pemasukan/{id}', [PemasukanApiController::class, 'update']);
    Route::delete('/pemasukan/{id}', [PemasukanApiController::class, 'destroy']);

    // ========================
    // PENGELUARAN
    // ========================
    Route::get('/pengeluaran', [PengeluaranApiController::class, 'index']);
    Route::post('/pengeluaran', [PengeluaranApiController::class, 'store']);
    Route::get('/pengeluaran/{id}', [PengeluaranApiController::class, 'show']);
    Route::put('/pengeluaran/{id}', [PengeluaranApiController::class, 'update']);
    Route::delete('/pengeluaran/{id}', [PengeluaranApiController::class, 'destroy']);

    // Scan struk (opsional)
    Route::post('/pengeluaran/scan', [PengeluaranApiController::class, 'scan']);

    // ========================
    // TARGET
    // ========================
    Route::get('/target', [TargetApiController::class, 'index']);
    Route::post('/target', [TargetApiController::class, 'store']);
    Route::get('/target/{id}', [TargetApiController::class, 'show']);
    Route::put('/target/{id}', [TargetApiController::class, 'update']);
    Route::delete('/target/{id}', [TargetApiController::class, 'destroy']);
    Route::post('/target/{id}/tercapai', [TargetApiController::class, 'updateTercapai']);

    // ========================
    // HISTORY
    // ========================
    Route::get('/history', [HistoryApiController::class, 'index']);
});
