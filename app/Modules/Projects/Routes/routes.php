<?php
// Path: app/Modules/Projects/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\Projects\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Modules\Projects\Projects\Http\Controllers\ProjectController;
use App\Modules\Projects\Tasks\Http\Controllers\TaskController;
use App\Modules\Projects\Timesheets\Http\Controllers\TimesheetController;

/**
 * Enterprise Projects Routes
 */
return static function (Router $router): void {
    
    $projects = new RouteGroup($router, [
        'prefix'     => 'api/v1/projects',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $projects->group(function (RouteGroup $group) {
        $group->get('/', [ProjectController::class, 'index']);
        $group->post('/create', [ProjectController::class, 'store']);
        $group->post('/tasks', [TaskController::class, 'store']);
        $group->post('/timesheets', [TimesheetController::class, 'store']);
    });
};