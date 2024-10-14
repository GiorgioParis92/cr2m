<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeneficiaireController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\Api\RdvController;
use App\Http\Controllers\Api\DossiersController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\OcrAnalyze;
use App\Http\Controllers\Api\YouSign;
use App\Http\Controllers\Api\YouSignStatus;
use App\Http\Controllers\Api\VRP;
use App\Http\Controllers\Api\Scrapping;
use App\Http\Controllers\Api\ScrappingAll;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});




Route::get('/vrp', [VRP::class, 'index']);
Route::post('/vrp', [VRP::class, 'index']);
Route::apiResource('beneficiaires', BeneficiaireController::class);

Route::get('/generate-pdf', [PDFController::class, 'generatePDF']);
Route::post('/generate-pdf', [PDFController::class, 'generatePDF']);

Route::get('/generate-config', [PDFController::class, 'generateConfig']);
Route::post('/generate-config', [PDFController::class, 'generateConfig']);

Route::get('/fill-pdf', [PDFController::class, 'fillPdf']);
Route::post('/fill-pdf', [PDFController::class, 'fillPdf']);
Route::get('/rdvs', [RdvController::class, 'index']);
Route::post('/rdvs', [RdvController::class, 'index']);
Route::post('/rdvs/save', [RdvController::class, 'save'])->name('rdv.save');
Route::post('/rdvs/update', [RdvController::class, 'update'])->name('rdv.update');

Route::post('/ocr-analyze', [OcrAnalyze::class, 'index']);
Route::post('/yousign', [YouSign::class, 'index']);
Route::post('/yousign-status', [YouSignStatus::class, 'index']);
Route::get('/yousign-status', [YouSignStatus::class, 'index']);

Route::get('/scrapping', [Scrapping::class, 'index']);
Route::post('/scrapping', [Scrapping::class, 'index']);

Route::get('/scrapping_all', [ScrappingAll::class, 'index']);
Route::post('/scrapping_all', [ScrappingAll::class, 'index']);

Route::get('/stats', [StatsController::class, 'index']);
Route::post('/stats', [StatsController::class, 'index']);

Route::middleware('auth:api')->group(function () {

    Route::get('/dossiers', [DossiersController::class, 'index']);
    Route::post('/dossiers', [DossiersController::class, 'index']);



    // Add other RdvController routes here as needed
});
