<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeneficiaireController;
use App\Http\Controllers\PDFController;


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


Route::apiResource('beneficiaires', BeneficiaireController::class);

Route::get('/generate-pdf', [PDFController::class, 'generatePDF']);
Route::post('/generate-pdf', [PDFController::class, 'generatePDF']);

Route::get('/fill-pdf', [PDFController::class, 'fillPdf']);
Route::post('/fill-pdf', [PDFController::class, 'fillPdf']);
