<?php
// Path: app/Modules/POS/Orders/Domain/Events/PosOrderCompletedEvent.php

declare(strict_types=1);

namespace App\Modules\POS\Orders\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: POS Order Completed
 * يُطلق فور إتمام بيعة الكاشير. 
 * مستمعي هذا الحدث:
 * 1. StockEngine لخصم الكميات من المخزن فوراً.
 * 2. AccountingEngine لإثبات الإيراد والمبيعات النقدية.
 */
class PosOrderCompletedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $shiftId;
    public readonly float $grandTotal;
    public readonly string $paymentMethod;

    public function __construct(int $orderId, int $companyId, int $shiftId, float $grandTotal, string $paymentMethod)
    {
        parent::__construct($orderId);
        $this->companyId = $companyId;
        $this->shiftId = $shiftId;
        $this->grandTotal = $grandTotal;
        $this->paymentMethod = $paymentMethod;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'     => $this->companyId,
            'shift_id'       => $this->shiftId,
            'grand_total'    => $this->grandTotal,
            'payment_method' => $this->paymentMethod,
        ]);
    }
}