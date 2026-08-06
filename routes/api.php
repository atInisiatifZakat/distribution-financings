<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inisiatif\Distribution\Financings\Http\Controllers;

Route::middleware('auth:sanctum')->prefix('api')->group(function (): void {
    Route::get('/distribution/project/{project}/financing', [Controllers\FinancingController::class, 'index']);
    Route::post('/distribution/project/{project}/financing/upload', [Controllers\UploadFinancingController::class, 'store']);
    Route::get('/distribution/project/financing/donation', [Controllers\DonationController::class, 'index']);
    Route::post('/distribution/project/financing', [Controllers\FinancingController::class, 'store']);
    Route::delete('/distribution/project/financing/{financing}', [Controllers\FinancingController::class, 'delete']);

    Route::get('/distribution/project/financing/options/funding-type', [Controllers\FundingTypeController::class, 'index']);
    Route::get('/distribution/project/financing/options/donation-program', [Controllers\DonationProgramController::class, 'index']);
});
