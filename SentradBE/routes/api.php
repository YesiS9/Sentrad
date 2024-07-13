<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\KategoriSeniController;
use App\Http\Controllers\Api\PenilaianKaryaController;
use App\Http\Controllers\Api\PenilaiController;
use App\Http\Controllers\Api\RegisterIndividuController;
use App\Http\Controllers\Api\RegisterKelompokController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RubrikController;
use App\Http\Controllers\Api\SeniController;
use App\Http\Controllers\Api\SenimanController;
use App\Http\Controllers\Api\TingkatanController;
use App\Http\Controllers\Api\UserController;
use App\Models\RegistrasiIndividu;

// Route untuk register, login, dan verifikasi email
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('email/verify/{id}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::get('/roles', [AuthController::class, 'getRoles']);
Route::post('/user/store-byAdmin', [UserController::class, 'storeByAdmin']);

// Middleware untuk memproteksi rute-rute di bawah ini dengan autentikasi
Route::middleware('auth:api')->group(function () {
    Route::apiResource('penilai', PenilaiController::class);
    Route::apiResource('forum', ForumController::class);
    Route::apiResource('seni', SeniController::class);
    Route::apiResource('penilaian', PenilaianKaryaController::class);
    Route::apiResource('kategori-seni', KategoriSeniController::class); // Ganti dengan nama yang konsisten
    Route::apiResource('registerIndividu', RegisterIndividuController::class); // Ganti dengan nama yang konsisten
    Route::apiResource('registerKelompok', RegisterKelompokController::class); // Ganti dengan nama yang konsisten
    Route::apiResource('rubrik', RubrikController::class);
    Route::apiResource('tingkatan', TingkatanController::class);
    Route::apiResource('user', UserController::class);
    Route::apiResource('role', RoleController::class);
    Route::apiResource('seniman', SenimanController::class);
    Route::get('/penilai/download', [PenilaiController::class, 'downloadPenilai'])->name('penilai.downloadPenilai');
    Route::get('/penilai/laporan', [PenilaiController::class, 'showLaporan']);
    Route::get('/penilai/downloadLaporan', [PenilaiController::class, 'downloadPenilaiLaporan']);
    Route::post('/registerIndividu/storeByAdmin', [RegisterIndividuController::class, 'storebyAdmin']);
    Route::post('/registerKelompok/storeByAdmin', [RegisterKelompokController::class, 'storebyAdmin']);
    Route::get('/user-profile', function (Request $request) {
        return $request->user();
    });
});


?>
