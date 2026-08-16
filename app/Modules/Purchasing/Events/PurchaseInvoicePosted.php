<?php
// Path: app/Modules/Purchasing/Events/PurchaseInvoicePosted.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Purchase Invoice Posted
 * يُطلق بعد حفظ فاتورة المشتريات (مطالبة المورد) لتنبيه النظام المحاسبي بضرورة إثبات 
 * المديونية (Accounts Payable) في دفتر الأستاذ العام.
 */
class PurchaseInvoicePosted extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $supplierId;
    public readonly float $grandTotal;
    public readonly float $taxTotal;

    public function __construct(int $invoiceId, int $companyId, int $supplierId, float $grandTotal, float $taxTotal)
    {
        parent::__construct($invoiceId);
        $this->companyId = $companyId;
        $this->supplierId = $supplierId;
        $this->grandTotal = $grandTotal;
        $this->taxTotal = $taxTotal;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'  => $this->companyId,
            'supplier_id' => $this->supplierId,
            'grand_total' => $this->grandTotal,
            'tax_total'   => $this->taxTotal,
        ]);
    }
}