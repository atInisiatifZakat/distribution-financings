<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inisiatif\Distribution\Financings\Http\Controllers;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/distribution/project/financing', [Controllers\FinancingController::class, 'store']);
    Route::get('/distribution/project/financing/donation', [Controllers\FinancingController::class, 'index']);
    Route::delete('/distribution/project/financing/{financing}', [Controllers\FinancingController::class, 'delete']);
});
