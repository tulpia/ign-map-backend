<?php

use App\Http\Controllers\AvisController;
use App\Http\Controllers\TrailController;
use App\Http\Controllers\TrailListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Trails (Publicly readable, write operations handled in controller/middleware)
Route::apiResource('trails', TrailController::class);

// Nested Avis/Reviews (Auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('trails.avis', AvisController::class)->only([
        'store', 'update', 'destroy'
    ]);

    Route::apiResource('lists', TrailListController::class);
});

require __DIR__.'/auth.php';
