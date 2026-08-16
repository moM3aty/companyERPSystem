<?php
// Path: app/Modules/Accounting/Routes/routes.php

declare(strict_types=1);

use App\Core\Routing\Router;
use App\Modules\Accounting\Http\Controllers\AccountingDashboardController;
use App\Modules\Accounting\Http\Controllers\ChartOfAccountsController;
use App\Modules\Accounting\Http\Controllers\JournalEntryController;
use App\Modules\Accounting\Http\Controllers\CostCenterController;
use App\Modules\Accounting\Http\Controllers\TaxController;
use App\Modules\Accounting\Http\Controllers\InvoiceController;
use App\Modules\Accounting\Http\Controllers\ReconciliationController;

/**
 * Enterprise Module Routes: Accounting
 * يتم استدعاء هذا الملف برمجياً لتحميل كل مسارات المحاسبة المعزولة.
 */
return function (Router $router): void {
    
    // 1. Dashboard
    $router->get('/accounting', [AccountingDashboardController::class, 'index']);

    // 2. Chart of Accounts (COA)
    $router->get('/accounting/chart-of-accounts', [ChartOfAccountsController::class, 'index']);
    $router->get('/accounting/chart-of-accounts/create', [ChartOfAccountsController::class, 'create']);
    $router->post('/api/v1/accounting/accounts', [ChartOfAccountsController::class, 'store']);

    // 3. Journal Entries
    $router->get('/accounting/journal-entries', [JournalEntryController::class, 'index']);
    $router->get('/accounting/journal-entries/create', [JournalEntryController::class, 'create']);
    $router->post('/api/v1/accounting/journal-entries', [JournalEntryController::class, 'store']);

    // 4. Cost Centers
    $router->get('/accounting/cost-centers', [CostCenterController::class, 'index']);

    // 5. Taxes & VAT
    $router->get('/accounting/taxes', [TaxController::class, 'index']);

    // 6. Invoices (A/R View)
    $router->get('/accounting/invoices', [InvoiceController::class, 'index']);

    // 7. Bank Reconciliation
    $router->get('/accounting/reconciliation', [ReconciliationController::class, 'index']);
    $router->post('/api/v1/accounting/reconciliation', [ReconciliationController::class, 'store']);

};