<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inisiatif\Distribution\Financings\Http\Controllers;

Route::middleware('auth:sanctum')->prefix('api')->group(function (): void {
    Route::get('/distribution/project/{project}/financing', [Controllers\FinancingController::class, 'index']);
    Route::get('/distribution/project/financing/donation', [Controllers\DonationController::class, 'index']);
    Route::post('/distribution/project/financing', [Controllers\FinancingController::class, 'store']);
    Route::delete('/distribution/project/financing/{financing}', [Controllers\FinancingController::class, 'delete']);
});
