<?php
// Path: app/Modules/Inventory/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\Inventory\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Modules\Inventory\Products\Http\Controllers\ProductController;
use App\Modules\Inventory\Categories\Http\Controllers\CategoryController;
use App\Modules\Inventory\Warehouses\Http\Controllers\WarehouseController;
use App\Modules\Inventory\Stock\Http\Controllers\StockController;
use App\Modules\Inventory\StockMovements\Http\Controllers\StockMovementController;
use App\Modules\Inventory\Transfers\Http\Controllers\StockTransferController;
use App\Modules\Inventory\StockTaking\Http\Controllers\StockTakingController;

/**
 * Enterprise Inventory Routes
 * يتم تحميل هذا الملف آلياً لتهيئة كافة مسارات المستودعات.
 */
return static function (Router $router): void {
    
    $inventory = new RouteGroup($router, [
        'prefix'     => 'api/v1/inventory',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $inventory->group(function (RouteGroup $group) {
        
        // Products & Categories
        $group->get('/categories', [CategoryController::class, 'index']);
        $group->post('/categories', [CategoryController::class, 'store']);
        
        $group->get('/products', [ProductController::class, 'index']);
        $group->post('/products', [ProductController::class, 'store']);
        
        // Warehouses
        $group->get('/warehouses', [WarehouseController::class, 'index']);
        $group->post('/warehouses', [WarehouseController::class, 'store']);
        
        // Stock & Movements
        $group->get('/stock/{productId}/{warehouseId}', [StockController::class, 'show']);
        $group->get('/movements/{productId}/{warehouseId}', [StockMovementController::class, 'history']);
        
        // Operations (Transfers & Adjustments)
        $group->post('/transfers', [StockTransferController::class, 'store']);
        $group->post('/stock-taking', [StockTakingController::class, 'store']);
    });
};