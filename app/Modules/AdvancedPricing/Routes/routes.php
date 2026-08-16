<?php
// Path: app/Modules/AdvancedPricing/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\AdvancedPricing\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Modules\AdvancedPricing\Controllers\ContractPricingController;

/**
 * Enterprise Advanced Pricing Routes
 */
return static function (Router $router): void {
    
    $pricing = new RouteGroup($router, [
        'prefix'     => 'api/v1/advanced-pricing',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $pricing->group(function (RouteGroup $group) {
        $group->post('/contracts', [ContractPricingController::class, 'store']);
        // (يمكن إضافة مسارات PriceList و Discount Matrix المتقدمة هنا)
    });
};