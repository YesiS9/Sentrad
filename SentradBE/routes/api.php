<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\KaryaController;
use App\Http\Controllers\Api\KategoriSeniController;
use App\Http\Controllers\Api\PenilaianKaryaController;
use App\Http\Controllers\Api\PenilaiController;
use App\Http\Controllers\Api\PortofolioController;
use App\Http\Controllers\Api\RegisterIndividuController;
use App\Http\Controllers\Api\RegisterKelompokController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RubrikController;
use App\Http\Controllers\Api\SeniController;
use App\Http\Controllers\Api\SenimanController;
use App\Http\Controllers\Api\TingkatanController;
use App\Http\Controllers\Api\UserController;


// Route untuk register, login, dan verifikasi email
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('email/verify/{id}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::get('/roles', [AuthController::class, 'getRoles']);
Route::post('/user/store-byAdmin', [UserController::class, 'storeByAdmin']);
Route::post('seniman', [SenimanController::class, 'store']);

// Middleware untuk memproteksi rute-rute di bawah ini dengan autentikasi
Route::middleware('auth:api')->group(function () {
    Route::apiResource('penilai', PenilaiController::class);
    Route::apiResource('forum', ForumController::class);
    Route::apiResource('seni', SeniController::class);
    Route::apiResource('penilaian', PenilaianKaryaController::class);
    Route::apiResource('kategoriSeni', KategoriSeniController::class);
    Route::apiResource('registerIndividu', RegisterIndividuController::class);
    Route::apiResource('registerKelompok', RegisterKelompokController::class);
    Route::apiResource('rubrik', RubrikController::class);
    Route::apiResource('tingkatan', TingkatanController::class);
    Route::apiResource('user', UserController::class);
    Route::apiResource('role', RoleController::class);
    Route::apiResource('karya', KaryaController::class);
    Route::apiResource('portofolio', PortofolioController::class);
    Route::get('/karyas/{id}', [KaryaController::class, 'index']);
    Route::get('/seniman', [SenimanController::class, 'index']);
    Route::get('/rubrikPenilai', [RubrikController::class, 'indexByUser']);
    Route::put('/seniman/{id}', [SenimanController::class, 'update']);
    Route::get('/seniman/{id}', [SenimanController::class, 'show']);
    Route::delete('/seniman/{id}', [SenimanController::class, 'destroy']);
    Route::get('/nama-kategori', [KategoriSeniController::class, 'indexKategori']);
    Route::get('/userbypenilai', [UserController::class,'indexByPenilai']);
    Route::get('/userbyseniman', [UserController::class,'indexBySeniman']);
    Route::get('/portofolio/data/{id}', [PortofolioController::class, 'showData']);
    Route::get('/penilai/download', [PenilaiController::class, 'downloadPenilai'])->name('penilai.downloadPenilai');
    Route::get('/penilai/laporan', [PenilaiController::class, 'showLaporan']);
    Route::get('/penilai/downloadLaporan', [PenilaiController::class, 'downloadPenilaiLaporan']);
    Route::post('/seniman/storeByAdmin', [SenimanController::class, 'storebyAdmin']);
    Route::get('/seni-by-kategori/{kategoriNama}', [SeniController::class, 'getSeniByKategori']);
    Route::get('/registerIndividu/showByAdmin/{id}', [RegisterIndividuController::class, 'showByAdmin']);
    Route::get('/registerKelompok/showByAdmin/{id}', [RegisterKelompokController::class, 'showByAdmin']);
    Route::post('/registerIndividu/storeByAdmin', [RegisterIndividuController::class, 'storebyAdmin']);
    Route::post('/registerKelompok/storeByAdmin', [RegisterKelompokController::class, 'storebyAdmin']);
    Route::put('/registerIndividu/updateByAdmin/{id}', [RegisterIndividuController::class, 'updateByAdmin']);
    Route::put('/registerKelompok/storeByAdmin/{id}', [RegisterKelompokController::class, 'updateByAdmin']);
    Route::get('/registerIndividuUser', [RegisterIndividuController::class, 'getRegistrasiIndividu']);
    Route::get('/registerKelompokUser', [RegisterKelompokController::class, 'getRegistrasiKelompok']);
    Route::get('/registerIndividuPenilai', [RegisterIndividuController::class, 'indexForPenilai']);
    Route::get('/registerKelompokPenilai', [RegisterKelompokController::class, 'indexForPenilai']);
    Route::get('/indexByRegis', [PortofolioController::class, 'filterByRegistrasi']);

    Route::get('/user-profile', function (Request $request) {
        return $request->user();
    });
});


?>
