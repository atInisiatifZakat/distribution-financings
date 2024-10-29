<?php

declare(strict_types=1);

use Illuminate\Routing\Router;
use Inisiatif\Distribution\Financings\Http\Controllers;

return static function (Router $router): void {
    $router->middleware('auth:sanctum')->group(function (Router $router): void {
        $router->post('/distribution/project/financing', [Controllers\FinancingController::class, 'store']);

        $router->get('/distribution/project/financing/donation', [Controllers\FinancingController::class, 'index']);
        $router->delete('/distribution/project/financing/{financing}', [Controllers\FinancingController::class, 'delete']);
    });
};
