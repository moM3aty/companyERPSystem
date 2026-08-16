<?php
// Path: app/Modules/AdvancedHR/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\AdvancedHR\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
// use App\Modules\AdvancedHR\Controllers\CompetencyController;

/**
 * Enterprise Advanced HR Routes
 */
return static function (Router $router): void {
    
    $hr = new RouteGroup($router, [
        'prefix'     => 'api/v1/advanced-hr',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $hr->group(function (RouteGroup $group) {
        // (يمكن ربط الكنترولرات الخاصة بالـ Competency والـ Succession هنا)
        // $group->post('/competencies/assess', [CompetencyController::class, 'assess']);
    });
};