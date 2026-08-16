<?php
// Path: app/Modules/Purchasing/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Modules\Purchasing\Suppliers\Http\Controllers\SupplierController;
use App\Modules\Purchasing\Requisitions\Http\Controllers\PurchaseRequisitionController;
use App\Modules\Purchasing\RFQ\Http\Controllers\RfqController;
use App\Modules\Purchasing\PurchaseOrders\Http\Controllers\PurchaseOrderController;
use App\Modules\Purchasing\GoodsReceipts\Http\Controllers\GoodsReceiptController;
use App\Modules\Purchasing\Invoices\Http\Controllers\PurchaseInvoiceController;

/**
 * Enterprise Purchasing Routes
 * يتم تحميل هذا الملف آلياً لتهيئة كافة مسارات المشتريات والموردين.
 */
return static function (Router $router): void {
    
    $purchasing = new RouteGroup($router, [
        'prefix'     => 'api/v1/purchasing',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $purchasing->group(function (RouteGroup $group) {
        
        // Suppliers (Vendors)
        $group->get('/suppliers', [SupplierController::class, 'index']);
        $group->post('/suppliers', [SupplierController::class, 'store']);
        
        // Internal Purchase Requisitions (PR)
        $group->post('/requisitions', [PurchaseRequisitionController::class, 'store']);
        
        // Request for Quotations (RFQ)
        $group->post('/rfqs', [RfqController::class, 'store']);
        
        // Purchase Orders (PO)
        $group->post('/purchase-orders', [PurchaseOrderController::class, 'store']);
        
        // Goods Receipt Notes (GRN)
        $group->post('/goods-receipts', [GoodsReceiptController::class, 'store']);
        
        // Purchase Invoices (Bills)
        $group->post('/invoices', [PurchaseInvoiceController::class, 'store']);
    });
};