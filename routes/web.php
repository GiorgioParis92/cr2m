<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard2Controller;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\BeneficiaireController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\PDFController;
use setasign\Fpdi\TcpdfFpdi;
use App\Services\FileUploadService;
use App\FormModel\FormConfigHandler;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\EventController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);



Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/users/index', [UserController::class, 'index'])->name('users.index');
    Route::post('/user/create', [UserController::class, 'createUser'])->name('users.create');
    Route::put('/user/edit/{id}', [UserController::class, 'editUser'])->name('users.edit');
    Route::put('/user/destroy/{id}', [UserController::class, 'editUser'])->name('users.destroy');
    Route::post('/password/reset', [UserController::class, 'resetPassword']);
    
    Route::get('/profile/{id}', [ProfileController::class, 'edit'])->name('users.profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('campagnes/create', [CampagneController::class, 'create'])->name('campagnes.create');
    Route::post('campagnes', [CampagneController::class, 'store'])->name('campagnes.store');
    Route::get('campagnes/{id}/edit', [CampagneController::class, 'edit'])->name('campagnes.edit');
    Route::put('campagnes/{id}', [CampagneController::class, 'update'])->name('campagnes.update');
    Route::post('/campagnes/upload', [CampagneController::class, 'upload'])->name('campagnes.upload');
    Route::post('/docs/upload/{campagne}', [DocController::class, 'upload'])->name('docs.upload');


    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('clients/upload_logo', [ClientController::class, 'uploadLogo'])->name('clients.upload_logo');
    Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('clients/store', [ClientController::class, 'store'])->name('clients.store');
    Route::get('clients/{id}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('clients/{id}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');

    Route::resource('devis', DevisController::class);
    Route::get('devis/{id}/pdf', [DevisController::class, 'generatePdf'])->name('devis.pdf');

    Route::get('lang/{locale}', [LanguageController::class, 'switchLang'])->name('lang.switch');


    Route::resource('beneficiaires', BeneficiaireController::class);

    Route::get('dossier/', [DossierController::class, 'index'])->name('dossiers.index');

    Route::get('dossier/show/{id}', [DossierController::class, 'show'])->name('dossiers.show');
    Route::get('dossier/next_step/{id}', [DossierController::class, 'next_step'])->name('dossiers.next_step');


    // Route::get('/form/{id}', [FormController::class, 'show'])->name('form.show');
    Route::post('/form/save/{dossierId}', [DossierController::class, 'save_form'])->name('form.save');


    Route::get('/generate-pdf', [PDFController::class, 'generatePDF']);
    Route::post('/generate-pdf', [PDFController::class, 'generatePDF']);

    Route::get('/fill-pdf', [PDFController::class, 'fillPdf']);

    Route::post('upload_file', [FileUploadService::class, 'storeImage'])->name('upload_file');


    // routes/web.php
Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index']);
Route::get('/events', [App\Http\Controllers\EventController::class, 'getEvents']);


});

// Include auth routes like login, register, password reset, etc.
require __DIR__ . '/auth.php';
