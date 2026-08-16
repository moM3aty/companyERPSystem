<?php
// Path: app/Modules/CRM/Listeners/UpdateCustomerStatus.php

declare(strict_types=1);

namespace App\Modules\CRM\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Database\DatabaseManager;
use App\Modules\Sales\SalesOrders\Domain\Events\OrderConfirmedEvent; // Assuming event exists

/**
 * Enterprise Listener: Update Customer Status
 * يقوم بتحويل حالة العميل من (محتمل) إلى (نشط/مشتري) فور تأكيد أول أمر بيع له.
 */
class UpdateCustomerStatus implements EventListener
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function handle(Event $event): void
    {
        // We listen to OrderConfirmed or OpportunityWon depending on business logic
        // This is a generic pattern for state updates triggered by domain events
        if (!property_exists($event, 'customerId')) {
            return; 
        }

        $customerId = (int) $event->customerId;

        $this->db->connection()->update(
            "UPDATE customers SET status = 'active_buyer', updated_at = ? WHERE id = ? AND status = 'prospect'",
            [date('Y-m-d H:i:s'), $customerId]
        );
    }
}