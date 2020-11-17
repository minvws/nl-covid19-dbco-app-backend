<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', array('as' => 'login', function() {
    return view('login');
}));

// All pages that are behind auth
Route::middleware('auth')->group(function() {
    // Home (case overview)
    Route::get('/', [CaseController::class, 'listCases']);

    // Creating cases
    Route::get('/newcase', [Casecontroller::class, 'newCase']);
    Route::get('/editcase/{uuid}', [CaseController::class, 'editCase']);
    Route::post('/savecase', [CaseController::class, 'saveCase']);

    // Editing open cases
    Route::get('/case/{uuid}', [CaseController::class, 'viewCase']);

    // Create a pairing code
    Route::get('/paircase/{caseUuid}', [CaseController::class, 'pairCase']);

    // Dump data for export to HPZone
    Route::get('/dumpcase/{uuid}', [CaseController::class, 'dumpCase']);
    Route::post('/linktasktoexport', [TaskController::class, 'linkTaskToExport']);

    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::get('/task/{uuid}/questionnaire', [TaskController::class, 'viewTaskQuestionnaire']);
});

Route::get('auth/identityhub', [LoginController::class, 'redirectToProvider']);
Route::get('auth/login', [LoginController::class, 'handleProviderCallback']);

// Temporary development login stub so you can test the portal without ggd account.
Route::get('auth/stub', [LoginController::class, 'stubAuthenticate']);

// Liveness check for k8s
Route::get('/status', function() {
    return 'OK';
});
