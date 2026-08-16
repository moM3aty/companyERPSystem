<?php
// Path: app/Modules/Accounting/Database/Seeders/AccountingDefaultsSeeder.php

declare(strict_types=1);

namespace App\Modules\Accounting\Database\Seeders;

class AccountingDefaultsSeeder
{
    public function run(int $companyId): void
    {
        // 1. زرع شجرة الحسابات الأساسية
        $coaSeeder = new ChartOfAccountsSeeder();
        $coaSeeder->run($companyId);

        // 2. زرع الضرائب الأساسية
        $taxSeeder = new TaxSeeder();
        $taxSeeder->run($companyId);
        
        // يمكننا لاحقاً إضافة بذور للـ Cost Centers والـ Fiscal Years
    }
}