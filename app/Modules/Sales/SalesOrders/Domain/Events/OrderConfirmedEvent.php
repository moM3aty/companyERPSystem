<?php
// Path: app/Modules/Sales/SalesOrders/Domain/Events/OrderConfirmedEvent.php

declare(strict_types=1);

namespace App\Modules\Sales\SalesOrders\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Order Confirmed
 * يُطلق بعد اعتماد أمر البيع، ويستمع له "UpdateCustomerStatus" لتحويل حالة العميل المحتمل إلى مشتري.
 */
class OrderConfirmedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $customerId;

    public function __construct(int $orderId, int $companyId, int $customerId)
    {
        parent::__construct($orderId);
        $this->companyId = $companyId;
        $this->customerId = $customerId;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'  => $this->companyId,
            'customerId'  => $this->customerId, // استخدمنا هذا الاسم ليتطابق مع الـ Listener
        ]);
    }
}