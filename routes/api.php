<?php

declare(strict_types=1);

use Illuminate\Routing\Router;
use Inisiatif\Distribution\Financings\Http\Controllers;

return static function (Router $router): void {
    $router->middleware('auth:sanctum')->group(function (Router $router): void {
        $router->group([
            'prefix' => 'distribution',
        ], static function (Router $router): void {
            $router->group([
                'prefix' => 'project',
            ], static function (Router $router): void {
                $router->group([
                    'prefix' => 'financing',
                ], static function (Router $router): void {
                    $router->post('/', [Controllers\FinancingController::class, 'store']);

                    $router->get('donation', [Controllers\FinancingController::class, 'index']);
                    $router->delete('/{financing}', [Controllers\FinancingController::class, 'delete']);
                });
            });
        });
    });
};
