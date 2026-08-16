<?php
// Path: app/Modules/Accounting/Http/Controllers/AccountingDashboardController.php

declare(strict_types=1);

namespace App\Modules\Accounting\Http\Controllers;

use App\Core\Http\Request;
use App\Modules\Accounting\Core\AccountingService;

class AccountingDashboardController
{
    public function __construct(
        private readonly AccountingService $accountingService
    ) {}

    public function index(Request $request): void
    {
        // 1. Get current tenant/company (Simulated as 1 for now)
        $companyId = 1;

        // 2. Fetch high-level financial KPIs via AccountingService
        // In a real scenario, this would call specialized read-models or aggregated queries
        $kpis = [
            'cash_bank_balance' => 1845200.00,
            'accounts_receivable' => 450300.00,
            'accounts_payable' => 215400.00,
            'net_income_mtd' => 85420.00,
        ];

        // 3. Render the View
        require BASE_PATH . '/resources/views/accounting/index.php';
    }
}