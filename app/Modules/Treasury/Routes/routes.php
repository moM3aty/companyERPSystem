<?php
// Path: app/Modules/Treasury/Routes/routes.php

declare(strict_types=1);

namespace App\Modules\Treasury\Routes;

use App\Core\Routing\Router;
use App\Core\Routing\RouteGroup;

// استدعاء الكنترولرات بمساراتها المعمارية الصحيحة (DDD) التي تم إنشاؤها مسبقاً
use App\Modules\Treasury\Controllers\DashboardController;
use App\Modules\Treasury\Controllers\TransferController;
use App\Modules\Treasury\Receipts\Http\Controllers\ReceiptController;
use App\Modules\Treasury\Payments\Http\Controllers\PaymentVoucherController;
use App\Modules\Treasury\Accounts\Http\Controllers\TreasuryAccountController;
use App\Modules\Treasury\Reconciliation\Http\Controllers\ReconciliationController;

/**
 * Enterprise Treasury Routes
 * تم تصحيح المسارات المعمارية لضمان عمل الكنترولرات بدون أخطاء Undefined Type.
 */
return static function (Router $router): void {
    
    $treasury = new RouteGroup($router, [
        'prefix'     => 'api/v1/treasury',
        'middleware' => ['api', 'auth', 'tenant']
    ]);

    $treasury->group(function (RouteGroup $group) {
        
        // Dashboard
        $group->get('/dashboard', [DashboardController::class, 'index']);
        
        // Cash & Bank Accounts
        $group->get('/accounts', [TreasuryAccountController::class, 'index']);
        
        // Internal Transfers (Cash to Bank, Bank to Cash)
        $group->post('/transfers', [TransferController::class, 'store']);
        
        // Receipts (Cash In)
        $group->post('/receipts', [ReceiptController::class, 'store']);
        
        // Payments (Cash Out)
        $group->post('/payments', [PaymentVoucherController::class, 'store']);

        // Bank Reconciliation
        $group->post('/reconciliation/{statementId}/auto-match', [ReconciliationController::class, 'autoMatch']);
    });
};