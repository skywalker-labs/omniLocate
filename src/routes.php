<?php

use Illuminate\Support\Facades\Route;
use Skywalker\Location\Http\Controllers\HybridLocationController;

Route::group([
    'prefix' => 'omni-locate',
    'as' => 'omni-locate.',
    'middleware' => ['web'], // or api? web usually implies session/cookie which might be needed for CSRF, but this is an API. Let's stick generic.
], function () {
    Route::post('/verify', [HybridLocationController::class, 'verify'])->name('verify');

    // Dashboard
    Route::get('/dashboard', [\Skywalker\Location\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/stats', [\Skywalker\Location\Http\Controllers\DashboardController::class, 'stats'])->name('dashboard.stats');
});

