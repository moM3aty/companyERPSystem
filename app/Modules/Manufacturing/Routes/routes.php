<?php
// Path: app/Modules/Manufacturing/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Modules\Manufacturing\BOM\Http\Controllers\BOMController;
use App\Modules\Manufacturing\ProductionOrders\Http\Controllers\ProductionOrderController;

/**
 * Enterprise Manufacturing Routes
 */
return static function (Router $router): void {
    
    $manufacturing = new RouteGroup($router, [
        'prefix'     => 'api/v1/manufacturing',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $manufacturing->group(function (RouteGroup $group) {
        $group->post('/bom', [BOMController::class, 'store']);
        $group->post('/production-orders', [ProductionOrderController::class, 'store']);
        $group->post('/production-orders/{id}/complete', [ProductionOrderController::class, 'complete']);
    });
};