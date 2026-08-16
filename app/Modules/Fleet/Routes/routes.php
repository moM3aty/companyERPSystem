<?php
// Path: app/Modules/Fleet/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\Fleet\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Modules\Fleet\Vehicles\Http\Controllers\VehicleController;
use App\Modules\Fleet\Trips\Http\Controllers\TripController;

/**
 * Enterprise Fleet Routes
 */
return static function (Router $router): void {
    
    $fleet = new RouteGroup($router, [
        'prefix'     => 'api/v1/fleet',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $fleet->group(function (RouteGroup $group) {
        $group->get('/vehicles', [VehicleController::class, 'index']);
        $group->post('/vehicles', [VehicleController::class, 'store']);
        $group->post('/trips', [TripController::class, 'store']);
        $group->post('/trips/{id}/complete', [TripController::class, 'complete']);
    });
};