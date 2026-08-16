<?php
// Path: app/Modules/Maintenance/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\Maintenance\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Modules\Maintenance\MaintenancePlans\Http\Controllers\MaintenancePlanController;
use App\Modules\Maintenance\WorkOrders\Http\Controllers\WorkOrderController;

/**
 * Enterprise Maintenance Routes
 */
return static function (Router $router): void {
    
    $maintenance = new RouteGroup($router, [
        'prefix'     => 'api/v1/maintenance',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $maintenance->group(function (RouteGroup $group) {
        $group->post('/plans', [MaintenancePlanController::class, 'store']);
        $group->post('/work-orders', [WorkOrderController::class, 'store']);
        $group->post('/work-orders/{id}/complete', [WorkOrderController::class, 'complete']);
    });
};