<?php
// Path: app/Modules/Purchasing/PurchaseOrders/Domain/Events/PurchaseOrderCreatedEvent.php

declare(strict_types=1);

namespace App\Modules\Purchasing\PurchaseOrders\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Purchase Order Created
 * يتم إطلاقه لإبلاغ الـ Workflow أو لإرسال إيميل للمورد.
 */
class PurchaseOrderCreatedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly float $grandTotal;

    public function __construct(int $poId, int $companyId, float $grandTotal)
    {
        parent::__construct($poId);
        $this->companyId = $companyId;
        $this->grandTotal = $grandTotal;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'  => $this->companyId,
            'grand_total' => $this->grandTotal,
        ]);
    }
}