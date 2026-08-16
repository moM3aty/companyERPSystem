<?php
// Path: app/Modules/Sales/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\Sales\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;

use App\Modules\Sales\Quotations\Http\Controllers\QuotationController;
use App\Modules\Sales\SalesOrders\Http\Controllers\SalesOrderController;
use App\Modules\Sales\Invoices\Http\Controllers\SalesInvoiceController;
use App\Modules\Sales\Deliveries\Http\Controllers\DeliveryNoteController;
use App\Modules\Sales\CreditNotes\Http\Controllers\CreditNoteController;
use App\Modules\Sales\CustomerPayments\Http\Controllers\CustomerPaymentController;

/**
 * Enterprise Sales Routes
 * يجمع كافة مسارات الدورة المستندية للمبيعات.
 */
return static function (Router $router): void {
    
    $sales = new RouteGroup($router, [
        'prefix'     => 'api/v1/sales',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $sales->group(function (RouteGroup $group) {
        
        // Pre-Sales
        $group->post('/quotations', [QuotationController::class, 'store']);
        
        // Order Management
        $group->post('/orders', [SalesOrderController::class, 'store']);
        
        // Fulfillment & Logistics
        $group->post('/deliveries', [DeliveryNoteController::class, 'store']);
        
        // Billing & Invoicing
        $group->post('/invoices', [SalesInvoiceController::class, 'store']);
        
        // Returns & Credit Notes
        $group->post('/credit-notes', [CreditNoteController::class, 'store']);
        
        // Customer Payments (AR Settlement)
        $group->post('/payments/allocate', [CustomerPaymentController::class, 'allocate']);
    });
};