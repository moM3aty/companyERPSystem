<?php
// Path: app/Modules/Sales/Listeners/UpdateCustomerBalance.php

declare(strict_types=1);

namespace App\Modules\Sales\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Database\DatabaseManager;
use App\Modules\Sales\Invoices\Domain\Events\InvoicePostedEvent;

/**
 * Enterprise Listener: Update Customer Balance
 * يقوم بتحديث رصيد المديونية للعميل (Denormalization) لسهولة العرض في الـ Dashboard
 * بمجرد ترحيل الفاتورة، بدلاً من حسابها (SUM) في كل مرة.
 */
class UpdateCustomerBalance implements EventListener
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof InvoicePostedEvent) {
            return;
        }

        $customerId = $event->customerId;
        $amount = $event->grandTotal;

        // زيادة رصيد المديونية للعميل
        $this->db->connection()->statement(
            "UPDATE customers SET current_balance = current_balance + ?, updated_at = ? WHERE id = ?",
            [$amount, date('Y-m-d H:i:s'), $customerId]
        );
    }
}