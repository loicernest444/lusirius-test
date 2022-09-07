<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/reports', [\App\Http\Controllers\ImageReportController::class, 'index'])->name('images');

Route::post('/report-image', [\App\Http\Controllers\ImageReportController::class, 'reportImage'])->name('report-image');

Route::delete('/destroy-image-report/{id}', [\App\Http\Controllers\ImageReportController::class, 'destroy'])->name('destroy-image');

Route::delete('/archive-image-report/{id}', [\App\Http\Controllers\ImageReportController::class, 'archive'])->name('archive-image');

Route::put('/approve-report/{id}', [\App\Http\Controllers\ImageReportController::class, 'approveOrRejectReport'])->name('approve');

Route::put('/update-report-callback/{id}', [\App\Http\Controllers\ImageReportController::class, 'update'])->name('update-callback');

Route::get('/reevaluate-report/{id}', [\App\Http\Controllers\ImageReportController::class, 'reevaluateExistingReport'])->name('reevaluate');

Route::get('/callback/{id}', [\App\Http\Controllers\ImageReportController::class, 'callCallback'])->name('callback');

Route::post('/callback-test', [\App\Http\Controllers\ImageReportController::class, 'callbackTester'])->name('callback-test');

