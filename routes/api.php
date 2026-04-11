<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Building\DataBaseBuildingController;
use App\Http\Controllers\Building\DataBaseBuildingFacilityController;
use App\Http\Controllers\Building\DataBaseBuildingRoomController;
use App\Http\Controllers\DataDocumentController;
use App\Http\Controllers\MasterData\MasterDataProgramStudiController;
use App\Http\Controllers\MasterData\MasterDataKelasController;
use App\Http\Controllers\MasterData\MasterDataMataKuliahController;
use App\Http\Controllers\MasterData\MasterDataMahasiswaController;
use App\Http\Controllers\MasterData\MasterDataDosenController;
use App\Http\Controllers\MasterData\MasterSksSettingController;
use App\Http\Controllers\MasterData\MasterOperasionalScheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user/profile', [AuthController::class, 'me']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('documents')->group(function () {
        Route::post('/upload', [DataDocumentController::class, 'store']);
    });

    Route::prefix('building')->group(function () {
        Route::get('/buildings-simple', [DataBaseBuildingController::class, 'listOnly']);
        Route::get('/buildings', [DataBaseBuildingController::class, 'index']);
        Route::apiResource('buildings', DataBaseBuildingController::class);
        Route::get('/rooms/{id}/facilities', [DataBaseBuildingRoomController::class, 'getFacilities']);
        Route::apiResource('rooms', DataBaseBuildingRoomController::class);
        Route::apiResource('facilities', DataBaseBuildingFacilityController::class);
    });
    Route::prefix('master-data')->group(function () {
        Route::apiResource('program-studi', MasterDataProgramStudiController::class);
        Route::apiResource('kelas', MasterDataKelasController::class);
        Route::apiResource('mata-kuliah', MasterDataMataKuliahController::class);
        Route::apiResource('mahasiswa', MasterDataMahasiswaController::class);
        Route::apiResource('dosen', MasterDataDosenController::class);
        Route::apiResource('sks-setting', MasterSksSettingController::class);
        Route::post('operasional-schedule/bulk-update', [MasterOperasionalScheduleController::class, 'bulkUpdate']);
        Route::apiResource('operasional-schedule', MasterOperasionalScheduleController::class);
    });
});