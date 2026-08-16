<?php
// Path: app/Core/Bootstrap/RepositoryServiceProvider.php

declare(strict_types=1);

namespace App\Core\Bootstrap;

// Admin
use App\Modules\Administration\Users\Domain\UserRepositoryInterface;
use App\Modules\Administration\Users\Infrastructure\UserRepository;
use App\Modules\Administration\Roles\Domain\RoleRepositoryInterface;
use App\Modules\Administration\Roles\Infrastructure\RoleRepository;
use App\Modules\Administration\Companies\Domain\CompanyRepositoryInterface;
use App\Modules\Administration\Companies\Infrastructure\CompanyRepository;
use App\Modules\Administration\Branches\Domain\BranchRepositoryInterface;
use App\Modules\Administration\Branches\Infrastructure\BranchRepository;

// Accounting
use App\Modules\Accounting\ChartOfAccounts\Domain\AccountRepositoryInterface;
use App\Modules\Accounting\ChartOfAccounts\Infrastructure\AccountRepository;
use App\Modules\Accounting\JournalEntries\Domain\JournalEntryRepositoryInterface;
use App\Modules\Accounting\JournalEntries\Infrastructure\JournalEntryRepository;

// CRM & Sales
use App\Modules\CRM\Customers\Domain\CustomerRepositoryInterface;
use App\Core\CRM\CustomerRepository; // Because it was placed in Core in your structure
use App\Modules\Sales\SalesOrders\Domain\SalesOrderRepositoryInterface;
use App\Modules\Sales\SalesOrders\Infrastructure\SalesOrderRepository;
use App\Modules\Sales\Invoices\Domain\SalesInvoiceRepositoryInterface;
use App\Modules\Sales\Invoices\Infrastructure\SalesInvoiceRepository;

// Inventory & Purchasing
use App\Modules\Inventory\Stock\Domain\StockRepositoryInterface;
use App\Modules\Inventory\Stock\Infrastructure\StockRepository;
use App\Modules\Purchasing\PurchaseOrders\Domain\PurchaseOrderRepositoryInterface;
use App\Modules\Purchasing\PurchaseOrders\Infrastructure\PurchaseOrderRepository;

/**
 * Enterprise Service Provider: Repositories
 * يقوم بربط الواجهات (Interfaces) بالكلاسات الفعلية (Concretes) لتفعيل الـ Dependency Injection.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Administration
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(BranchRepositoryInterface::class, BranchRepository::class);

        // Accounting
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->bind(JournalEntryRepositoryInterface::class, JournalEntryRepository::class);

        // CRM & Sales
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(SalesOrderRepositoryInterface::class, SalesOrderRepository::class);
        $this->app->bind(SalesInvoiceRepositoryInterface::class, SalesInvoiceRepository::class);

        // Inventory & Purchasing
        $this->app->bind(StockRepositoryInterface::class, StockRepository::class);
        $this->app->bind(PurchaseOrderRepositoryInterface::class, PurchaseOrderRepository::class);

        // ملاحظة: في النظام الفعلي، يتم إضافة باقي الـ Repositories هنا بنفس النمط (HR, Manufacturing, etc.)
    }
}