<?php
// Path: app/Modules/Purchasing/GoodsReceipts/Domain/Events/GoodsReceiptProcessedEvent.php

declare(strict_types=1);

namespace App\Modules\Purchasing\GoodsReceipts\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: GRN Processed
 * يُطلق هذا الحدث لتبليغ الـ Accounting Engine بتسجيل (Inventory Accrual)
 * أو لتبليغ الـ PO بتحديث حالة الاستلام.
 */
class GoodsReceiptProcessedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $supplierId;

    public function __construct(int $receiptId, int $companyId, int $supplierId)
    {
        parent::__construct($receiptId);
        $this->companyId = $companyId;
        $this->supplierId = $supplierId;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'  => $this->companyId,
            'supplier_id' => $this->supplierId,
        ]);
    }
}