<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\BeneficiaireController;
use App\Http\Controllers\Api\DossiersDataController;
use App\Http\Controllers\Api\Dossiers;
use App\Http\Controllers\Api\EtapesController;
use App\Http\Controllers\Api\FicheController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\FormsConfigController;
// routes/api.php
use App\Http\Controllers\Api\FormsDataController;
use App\Http\Controllers\Api\RdvControllerPhone;
use App\Http\Controllers\Api\RdvStatusController;
use App\Http\Controllers\Api\RdvTypeController;
use App\Http\Controllers\Api\UpdatePermission;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\TablesController;

use App\Http\Controllers\Api\DynamicModelController;
use App\Http\Controllers\Api\UploadFile;
use App\Http\Controllers\Api\SubscriptionController;


use App\Http\Controllers\AudioController;


use App\Http\Controllers\Api\CallBack;






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
Route::post('/login', [LoginController::class, 'login']);

Route::get('/server-callback', CallBack::class)
     ->name('server-callback');
     Route::post('/server-callback', CallBack::class)
     ->name('server-callback');
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
Route::post('/charts/{chartId}', [StatsController::class, 'getChartData']);

Route::middleware('auth:api')->group(function () {



    Route::get('/dossiers-rdv', [DossiersController::class, 'index']);
    Route::post('/dossiers-rdv', [DossiersController::class, 'index']);


    Route::post('/audio/analyse', [AudioController::class, 'analyse'])->name('audio.analyse');


//     Route::get('/beneficiaires', [BeneficiaireController::class, 'index']);
//     Route::get('/beneficiaires/{id}', [BeneficiaireController::class, 'show']);
//     Route::post('/beneficiaires', [BeneficiaireController::class, 'store']);
//     Route::put('/beneficiaires/{id}', [BeneficiaireController::class, 'update']);
//     Route::delete('/beneficiaires/{id}', [BeneficiaireController::class, 'destroy']);

//     Route::get('/dossiers_data', [DossiersDataController::class, 'index']);
//     Route::get('/dossiers_data/{id}', [DossiersDataController::class, 'show']);
//     Route::post('/dossiers_data', [DossiersDataController::class, 'store']);
//     Route::put('/dossiers_data/{id}', [DossiersDataController::class, 'update']);
//     Route::delete('/dossiers_data/{id}', [DossiersDataController::class, 'destroy']);


    // Route::get('/dossiers', [Dossiers::class, 'index']);
    // Route::get('/dossiers/{id}', [Dossiers::class, 'show']);
    // Route::post('/dossiers', [Dossiers::class, 'store']);
    // Route::put('/dossiers/{id}', [Dossiers::class, 'update']);
    // Route::delete('/dossiers/{id}', [Dossiers::class, 'destroy']);

//     Route::get('/etapes', [EtapesController::class, 'index']);
//     Route::get('/etapes/{id}', [EtapesController::class, 'show']);
//     Route::post('/etapes', [EtapesController::class, 'store']);
//     Route::put('/etapes/{id}', [EtapesController::class, 'update']);
//     Route::delete('/etapes/{id}', [EtapesController::class, 'destroy']);


//     Route::get('/fiches', [FicheController::class, 'index']);
// Route::get('/fiches/{id}', [FicheController::class, 'show']);
// Route::post('/fiches', [FicheController::class, 'store']);
// Route::put('/fiches/{id}', [FicheController::class, 'update']);
// Route::delete('/fiches/{id}', [FicheController::class, 'destroy']);

// Route::get('/forms', [FormController::class, 'index']);
// Route::get('/forms/{id}', [FormController::class, 'show']);
// Route::post('/forms', [FormController::class, 'store']);
// Route::put('/forms/{id}', [FormController::class, 'update']);
// Route::delete('/forms/{id}', [FormController::class, 'destroy']);



// Route::get('/forms_config', [FormsConfigController::class, 'index']);
// Route::get('/forms_config/{id}', [FormsConfigController::class, 'show']);
// Route::post('/forms_config', [FormsConfigController::class, 'store']);
// Route::put('/forms_config/{id}', [FormsConfigController::class, 'update']);
// Route::delete('/forms_config/{id}', [FormsConfigController::class, 'destroy']);

// Route::get('/forms_data', [FormsDataController::class, 'index']);
// Route::get('/forms_data/{id}', [FormsDataController::class, 'show']);
// Route::post('/forms_data', [FormsDataController::class, 'store']);
// Route::put('/forms_data/{id}', [FormsDataController::class, 'update']);
// Route::delete('/forms_data/{id}', [FormsDataController::class, 'destroy']);

// Route::get('/rdv', [RdvControllerPhone::class, 'index']);
// Route::get('/rdv/{id}', [RdvControllerPhone::class, 'show']);
// Route::post('/rdv', [RdvControllerPhone::class, 'store']);
// Route::put('/rdv/{id}', [RdvControllerPhone::class, 'update']);
// Route::delete('/rdv/{id}', [RdvControllerPhone::class, 'destroy']);


// Route::get('/rdv_status', [RdvStatusController::class, 'index']);
// Route::get('/rdv_status/{id}', [RdvStatusController::class, 'show']);
// Route::post('/rdv_status', [RdvStatusController::class, 'store']);
// Route::put('/rdv_status/{id}', [RdvStatusController::class, 'update']);
// Route::delete('/rdv_status/{id}', [RdvStatusController::class, 'destroy']);

// Route::get('/rdv_type', [RdvTypeController::class, 'index']);
// Route::get('/rdv_type/{id}', [RdvTypeController::class, 'show']);
// Route::post('/rdv_type', [RdvTypeController::class, 'store']);
// Route::put('/rdv_type/{id}', [RdvTypeController::class, 'update']);
// Route::delete('/rdv_type/{id}', [RdvTypeController::class, 'destroy']);


// Route::get('/clients', [ClientController::class, 'index']);
// Route::get('/clients/{id}', [ClientController::class, 'show']);
// Route::post('/clients', [ClientController::class, 'store']);
// Route::put('/clients/{id}', [ClientController::class, 'update']);
// Route::delete('/clients/{id}', [ClientController::class, 'destroy']);

Route::post('/upload_file', [UploadFile::class, 'index']);
Route::post('/delete_file', [UploadFile::class, 'deleteFile']);
Route::post('/update_permission', [UpdatePermission::class, 'index']);

Route::get('/tables', [TablesController::class, 'index']);

// Dynamic routes for any model
Route::get('/{model}', [DynamicModelController::class, 'index']);
Route::get('/{model}/{id}', [DynamicModelController::class, 'show']);
Route::post('/{model}', [DynamicModelController::class, 'store']);
Route::put('/{model}/{id}', [DynamicModelController::class, 'update']);
Route::delete('/{model}/{id}', [DynamicModelController::class, 'destroy']);


Route::post('/{model}/updateOrInsert', [DynamicModelController::class, 'updateOrInsert']);
Route::post('/{model}/update_value', [DynamicModelController::class, 'updateOrInsertValue']);
});
