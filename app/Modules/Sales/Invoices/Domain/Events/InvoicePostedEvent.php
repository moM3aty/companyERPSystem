<?php
// Path: app/Modules/Sales/Invoices/Domain/Events/InvoicePostedEvent.php

declare(strict_types=1);

namespace App\Modules\Sales\Invoices\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Invoice Posted
 * يُطلق بعد اعتماد فاتورة المبيعات لتنبيه النظام المحاسبي (لعمل القيد) ونظام العملاء (لزيادة المديونية).
 */
class InvoicePostedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $customerId;
    public readonly float $grandTotal;

    public function __construct(int $invoiceId, int $companyId, int $customerId, float $grandTotal)
    {
        parent::__construct($invoiceId);
        $this->companyId = $companyId;
        $this->customerId = $customerId;
        $this->grandTotal = $grandTotal;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'  => $this->companyId,
            'customer_id' => $this->customerId,
            'grand_total' => $this->grandTotal,
        ]);
    }
}