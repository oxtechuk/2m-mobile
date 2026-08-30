<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SyncController;

/*
|--------------------------------------------------------------------------
| API Sync Routes for 2M Mobile Offline-First System
|--------------------------------------------------------------------------
*/

Route::prefix('v1/sync')->group(function () {
    Route::get('/ping', [SyncController::class, 'ping'])->name('api.sync.ping');
    Route::post('/push', [SyncController::class, 'push'])->name('api.sync.push');
    Route::get('/pull', [SyncController::class, 'pull'])->name('api.sync.pull');
});
