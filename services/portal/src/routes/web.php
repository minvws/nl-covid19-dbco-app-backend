<?php

use App\Http\Controllers\StatusController;
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

Route::get('/login', [LoginController::class, 'login'])->name('login');

// All pages that are behind auth and require user role
Route::middleware(['auth', 'rolecheck:user'])->group(function() {
    // Home (case overview)
    Route::get('/', [CaseController::class, 'listCases'])->name('cases-list');

    // Creating cases
    Route::get('/newcase', [Casecontroller::class, 'newCase'])->name('case-new');
    Route::get('/editcase/{uuid}', [CaseController::class, 'editCase'])->name('case-edit');
    Route::post('/savecase', [CaseController::class, 'saveCase'])->name('case-save');

    // Editing open cases
    Route::get('/case/{uuid}', [CaseController::class, 'viewCase'])->name('case-view');
    Route::get('/task/{taskUuid}/questionnaire', [TaskController::class, 'viewTaskQuestionnaire'])->name('task-questionnaire-view');
    Route::post('/task/{taskUuid}/questionnaire', [TaskController::class, 'saveTaskQuestionnaire'])->name('task-questionnaire-save');

    // Create a pairing code
    Route::get('/paircase/{caseUuid}', [CaseController::class, 'pairCase'])->name('case-pair');

    // Dump data for export to HPZone
    Route::get('/dumpcase/{uuid}', [CaseController::class, 'dumpCase'])->name('case-dump');
    Route::post('/linkcasetoexport', [CaseController::class, 'linkCaseToExport']);
    Route::post('/linktasktoexport', [TaskController::class, 'linkTaskToExport']);
    Route::post('/markascopied', [CaseController::class, 'markAsCopied']);
    Route::post('/assigncase', [CaseController::class, 'assignCase']);

    // Trigger to export case to GGD private API
    Route::get('/notifycaseupdate/{uuid}', [CaseController::class, 'notifyCaseUpdate'])->name('notify-case-update');
});

// All pages that are behind auth only
Route::middleware('auth')->group(function () {
    // Account
    Route::get('/profile', [UserController::class, 'profile'])->name('user-profile');
    Route::post('/logout', [LoginController::class, 'logout'])->name('user-logout');

    Route::get('/task/{uuid}/questionnaire', [TaskController::class, 'viewTaskQuestionnaire'])->name('task-questionnaire-view');
});


Route::get('auth/identityhub', [LoginController::class, 'redirectToProvider']);
Route::get('auth/login', [LoginController::class, 'handleProviderCallback']);

// Temporary development login stub so you can test the portal without ggd account.
Route::get('auth/stub', [LoginController::class, 'stubAuthenticate']);

// Liveness check for k8s
Route::get('/ping', [StatusController::class, 'ping']);
// Route::get('/status', [StatusController::class, 'status']); // TODO: first discuss blocking this url in k8s
