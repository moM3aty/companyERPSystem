<?php
// Path: app/Modules/SupplyChain/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\SupplyChain\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Modules\SupplyChain\Controllers\DemandController;

/**
 * Enterprise Supply Chain Routes
 */
return static function (Router $router): void {
    
    $supplyChain = new RouteGroup($router, [
        'prefix'     => 'api/v1/supply-chain',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $supplyChain->group(function (RouteGroup $group) {
        // تشغيل محرك التنبؤ بالمبيعات
        $group->post('/demand/forecast', [DemandController::class, 'runForecast']);
        
        // (يمكن إضافة مسارات الـ Safety Stock والـ Landed Costs المرتبطة بالسلاسل الإمداد لاحقاً)
    });
};