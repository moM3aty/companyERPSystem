<?php
// Path: app/Modules/Payroll/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\Payroll\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Modules\Payroll\Controllers\DashboardController;
use App\Modules\Payroll\PayrollRuns\Http\Controllers\PayrollController;

/**
 * Enterprise Payroll Routes
 */
return static function (Router $router): void {
    
    $payroll = new RouteGroup($router, ['prefix' => 'api/v1/payroll', 'middleware' => ['api', 'auth', 'tenant']]);
    
    $payroll->group(function (RouteGroup $group) {
        $group->get('/dashboard', [DashboardController::class, 'index']);
        $group->post('/runs', [PayrollController::class, 'store']);
    });
};