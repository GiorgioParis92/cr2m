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
use App\Http\Controllers\RdvController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EtapesController;
use App\Http\Controllers\Messagerie;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Livewire\Chat;


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
// Auth::routes(['verify' => true]);

// Password reset routes

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::get('/', 'App\Http\Controllers\DashboardController@index')->name('dashboard');
    Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index')->name('dashboard');


    Route::get('temporary-password/reset/{email}', [UserController::class, 'showTemporaryPasswordForm'])->name('temporary.password.reset');
Route::post('temporary-password/update', [UserController::class, 'updateTemporaryPassword'])->name('temporary.password.update');
});
// Route::middleware(['auth', 'temp_password'])->group(function () {
//     Route::get('temporary-password/reset', [UserController::class, 'showTemporaryPasswordForm'])->name('temporary.password.reset');
//     Route::post('temporary-password/update', [UserController::class, 'updateTemporaryPassword'])->name('temporary.password.update');
// });


Route::get('/chat', function () {
    return view('welcome'); // Remplacez par votre vue si nécessaire
})->middleware('auth');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);


    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');

    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
    
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');

    Route::get('/users/index', [UserController::class, 'index'])->name('users.index');
    Route::get('/user/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/user/store', [UserController::class, 'createUser'])->name('users.store');
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('users.editform');
    Route::put('/user/edit/{id}', [UserController::class, 'editUser'])->name('users.edit');
    Route::get('/user/destroy/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::delete('/user/destroy/{id}', [UserController::class, 'destroy'])->name('users.destroy');
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
    Route::post('beneficiaires/store', [BeneficiaireController::class, 'store'])->name('beneficiaires.store');


    Route::get('dossier/', [DossierController::class, 'index'])->name('dossiers.index');

    Route::get('dossier/show/{id}', [DossierController::class, 'show'])->name('dossiers.show');
    Route::get('dossier/delete/{id}', [DossierController::class, 'delete'])->name('dossiers.delete');
    Route::get('dossier/next_step/{id}', [DossierController::class, 'next_step'])->name('dossiers.next_step');


    // Route::get('/form/{id}', [FormController::class, 'show'])->name('form.show');
    Route::post('/form/save/{dossierId}', [DossierController::class, 'save_form'])->name('form.save');


    Route::get('/generate-pdf', [PDFController::class, 'generatePDF']);
    Route::post('/generate-pdf', [PDFController::class, 'generatePDF']);

    Route::get('/fill-pdf', [PDFController::class, 'fillPdf']);

    Route::post('upload_file', [FileUploadService::class, 'storeImage'])->name('upload_file');
    Route::post('delete_file', [FileUploadService::class, 'deleteImage'])->name('delete_file');
    Route::get('etapes-controller', [EtapesController::class, 'show'])->name('etapes-controller');
    Route::get('edit-etape/{id}', [EtapesController::class, 'edit'])->name('edit-etape');


    // routes/web.php
Route::get('/rdvs', [App\Http\Controllers\RdvController::class, 'index'])->name('rdvs');
Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index']);
Route::get('/events', [App\Http\Controllers\EventController::class, 'getEvents']);
Route::get('/messagerie', [App\Http\Controllers\Messagerie::class, 'index'])->name('messagerie');


Route::get('/search', [App\Http\Controllers\SearchController::class, 'search'])->name('search');


});

// Include auth routes like login, register, password reset, etc.
require __DIR__ . '/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
