<?php
// Path: routes/api.php

declare(strict_types=1);

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;
use App\Core\Http\Request;
use App\Core\Api\ApiResponse;

/**
 * =================================================================================
 * Enterprise ERP - API Routes
 * =================================================================================
 *
 * This file returns a route registrar closure.
 * RouteServiceProvider will pass the Router instance explicitly.
 */

return function (Router $router): void {

    /**
     * -------------------------------------------------------------------------
     * Health Check
     * -------------------------------------------------------------------------
     */
    $router->get('/api/health', function (Request $request) {
        return ApiResponse::success(
            [
                'status' => 'operational',
                'time'   => date('Y-m-d H:i:s'),
            ],
            'API is healthy.'
        );
    });

    /**
     * -------------------------------------------------------------------------
     * V1 Public API
     * -------------------------------------------------------------------------
     */
    $v1 = new RouteGroup(
        $router,
        [
            'prefix'     => 'api/v1',
            'middleware' => ['cors', 'api'],
        ]
    );

    /**
     * Authentication
     */
    $v1->post(
        '/auth/login',
        [
            \App\Modules\Administration\Users\Http\Controllers\UserController::class,
            'apiLogin',
        ]
    );

    /**
     * -------------------------------------------------------------------------
     * V1 Protected API
     * -------------------------------------------------------------------------
     *
     * We create a separate group using the same prefix and all required
     * middleware. This avoids accessing protected properties of RouteGroup.
     */
    $protected = new RouteGroup(
        $router,
        [
            'prefix' => 'api/v1',
            'middleware' => [
                'cors',
                'api',
                'auth',
                'tenant',
                'audit',
            ],
        ]
    );

    /**
     * Sales
     */
    $protected->post(
        '/sales/invoices',
        [
            \App\Modules\Sales\Invoices\Http\Controllers\SalesInvoiceController::class,
            'store',
        ]
    );

    $protected->post(
        '/sales/orders',
        [
            \App\Modules\Sales\SalesOrders\Http\Controllers\SalesOrderController::class,
            'store',
        ]
    );

    /**
     * Purchasing
     */
    $protected->post(
        '/purchasing/purchase-orders',
        [
            \App\Modules\Purchasing\PurchaseOrders\Http\Controllers\PurchaseOrderController::class,
            'store',
        ]
    );

    $protected->post(
        '/purchasing/goods-receipts',
        [
            \App\Modules\Purchasing\GoodsReceipts\Http\Controllers\GoodsReceiptController::class,
            'store',
        ]
    );

    /**
     * Inventory
     */
    $protected->get(
        '/inventory/stock/{product}/{warehouse}',
        [
            \App\Modules\Inventory\Stock\Http\Controllers\StockController::class,
            'show',
        ]
    );

    $protected->get(
        '/inventory/movements/{product}/{warehouse}',
        [
            \App\Modules\Inventory\StockMovements\Http\Controllers\StockMovementController::class,
            'history',
        ]
    );

    /**
     * HR
     */
    $protected->post(
        '/hr/leaves',
        [
            \App\Modules\HR\Leaves\Http\Controllers\LeaveController::class,
            'store',
        ]
    );

    $protected->post(
        '/hr/attendance/punch',
        [
            \App\Modules\HR\Attendance\Http\Controllers\AttendanceController::class,
            'punch',
        ]
    );

    /**
     * Accounting
     */
    $protected->post(
        '/accounting/journal-entries',
        [
            \App\Modules\Accounting\JournalEntries\Http\Controllers\JournalEntryController::class,
            'store',
        ]
    );

    $protected->post(
        '/accounting/year-closing',
        [
            \App\Modules\Accounting\YearClosing\Http\Controllers\YearClosingController::class,
            'close',
        ]
    );

    /**
     * CRM
     */
    $protected->post(
        '/crm/customers',
        [
            \App\Modules\CRM\Customers\Http\Controllers\CustomerController::class,
            'store',
        ]
    );
};