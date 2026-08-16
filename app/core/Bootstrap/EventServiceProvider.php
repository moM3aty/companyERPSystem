<?php
// Path: app/Core/Bootstrap/EventServiceProvider.php

declare(strict_types=1);

namespace App\Core\Bootstrap;

use App\Core\Events\EventRegistry;

// Events
use App\Modules\Administration\Users\Domain\Events\UserCreatedEvent;
use App\Modules\Sales\Invoices\Domain\Events\InvoicePostedEvent;
use App\Modules\Purchasing\Events\PurchaseInvoicePosted;
use App\Modules\Inventory\StockMovements\Domain\Events\StockUpdatedEvent;
use App\Modules\POS\Orders\Domain\Events\PosOrderCompletedEvent;

// Listeners
use App\Modules\Administration\Listeners\SendUserWelcomeNotification;
use App\Modules\Sales\Listeners\CreateAccountingEntry as SalesAccountingEntry;
use App\Modules\Sales\Listeners\UpdateCustomerBalance;
use App\Modules\Purchasing\Listeners\CreateAccountsPayableEntry;
use App\Modules\Inventory\Listeners\UpdateInventoryAnalytics;
use App\Modules\POS\Listeners\UpdateInventory as PosUpdateInventory;
use App\Modules\POS\Listeners\CreateAccountingEntry as PosAccountingEntry;

/**
 * Enterprise Service Provider: Events
 * يسجل مسارات الأحداث (Events) والمستمعين (Listeners).
 */
class EventServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // EventRegistry is registered as a singleton in AppServiceProvider
    }

    public function boot(): void
    {
        /** @var EventRegistry $registry */
        $registry = $this->app->make(EventRegistry::class);

        // Administration
        $registry->addListener(UserCreatedEvent::class, SendUserWelcomeNotification::class);

        // Sales Invoices
        $registry->addListener(InvoicePostedEvent::class, SalesAccountingEntry::class);
        $registry->addListener(InvoicePostedEvent::class, UpdateCustomerBalance::class);

        // Purchasing Invoices
        $registry->addListener(PurchaseInvoicePosted::class, CreateAccountsPayableEntry::class);

        // Inventory
        $registry->addListener(StockUpdatedEvent::class, UpdateInventoryAnalytics::class);

        // POS
        $registry->addListener(PosOrderCompletedEvent::class, PosUpdateInventory::class);
        $registry->addListener(PosOrderCompletedEvent::class, PosAccountingEntry::class);
    }
}