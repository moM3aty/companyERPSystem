<?php
// Path: app/Modules/POS/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\POS\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Modules\POS\Http\Controllers\PosController;
use App\Modules\POS\Shifts\Http\Controllers\ShiftClosingController;

/**
 * Enterprise POS Routes
 */
return static function (Router $router): void {
    
    $pos = new RouteGroup($router, [
        'prefix'     => 'api/v1/pos',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $pos->group(function (RouteGroup $group) {
        $group->post('/orders', [PosController::class, 'storeOrder']);
        $group->post('/shifts/{id}/close', [ShiftClosingController::class, 'close']);
    });
};