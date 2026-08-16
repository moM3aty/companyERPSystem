<?php
// Path: app/Core/Bootstrap/AuthServiceProvider.php

declare(strict_types=1);

namespace App\Core\Bootstrap;

use App\Core\Authorization\PolicyResolver;

// Policies
use App\Modules\Administration\Policies\UserPolicy;
use App\Modules\Accounting\Policies\AccountPolicy;
use App\Modules\Accounting\Policies\JournalEntryPolicy;
use App\Modules\Sales\Policies\SalesInvoicePolicy;
use App\Modules\Sales\Policies\SalesOrderPolicy;
use App\Modules\HR\Policies\EmployeePolicy;
use App\Modules\Inventory\Policies\WarehousePolicy;

/**
 * Enterprise Service Provider: Auth & Policies
 * يسجل السياسات الأمنية لحماية الموارد على مستوى السجل (Row-Level Security).
 */
class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PolicyResolver::class);
    }

    public function boot(): void
    {
        /** @var PolicyResolver $resolver */
        $resolver = $this->app->make(PolicyResolver::class);

        // ربط اسم الـ Resource بـ الـ Policy Class
        $resolver->register('users', UserPolicy::class);
        $resolver->register('chart_of_accounts', AccountPolicy::class);
        $resolver->register('journal_entries', JournalEntryPolicy::class);
        $resolver->register('sales_invoices', SalesInvoicePolicy::class);
        $resolver->register('sales_orders', SalesOrderPolicy::class);
        $resolver->register('hr_employees', EmployeePolicy::class);
        $resolver->register('warehouses', WarehousePolicy::class);
    }
}