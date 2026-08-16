<?php
// Path: app/Modules/FixedAssets/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Modules\FixedAssets\Assets\Http\Controllers\AssetController;
use App\Modules\FixedAssets\Depreciation\Http\Controllers\DepreciationController;

/**
 * Enterprise Fixed Assets Routes
 */
return static function (Router $router): void {
    
    $assets = new RouteGroup($router, [
        'prefix'     => 'api/v1/assets',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $assets->group(function (RouteGroup $group) {
        $group->post('/register', [AssetController::class, 'store']);
        $group->post('/depreciate', [DepreciationController::class, 'runDepreciation']);
    });
};