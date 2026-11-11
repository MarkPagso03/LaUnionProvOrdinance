<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrdinanceController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ResolutionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC AREA (no login needed, view-only)
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/ordinances/{ordinance}', [PublicController::class, 'showOrdinance'])->name('public.ordinances.show');
Route::get('/resolutions/{resolution}', [PublicController::class, 'showResolution'])->name('public.resolutions.show');

/*
|--------------------------------------------------------------------------
| LOGIN / LOGOUT
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| ADMINISTRATIVE AREA (login required)
| Guests are automatically redirected to the login page by the "auth" middleware.
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Batch import (CSV / Excel). These must come BEFORE the resource routes.
    Route::get('ordinances/import', [OrdinanceController::class, 'importForm'])->name('ordinances.import');
    Route::get('ordinances/import/template', [OrdinanceController::class, 'importTemplate'])->name('ordinances.import.template');
    Route::post('ordinances/import', [OrdinanceController::class, 'import'])->name('ordinances.import.store');

    Route::get('resolutions/import', [ResolutionController::class, 'importForm'])->name('resolutions.import');
    Route::get('resolutions/import/template', [ResolutionController::class, 'importTemplate'])->name('resolutions.import.template');
    Route::post('resolutions/import', [ResolutionController::class, 'import'])->name('resolutions.import.store');

    Route::resource('ordinances', OrdinanceController::class);
    Route::resource('resolutions', ResolutionController::class);
});
