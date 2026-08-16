<?php
// Path: app/Modules/Treasury/Events/FundsTransferred.php

declare(strict_types=1);

namespace App\Modules\Treasury\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Funds Transferred
 * يُطلق بعد حفظ التحويل وإنشاء القيد لإبلاغ مراقبي العمليات أو تنبيه الإدارة.
 */
class FundsTransferred extends DomainEvent
{
    public readonly int $companyId;
    public readonly float $amount;
    public readonly string $transferNo;

    public function __construct(int $transferId, int $companyId, float $amount, string $transferNo)
    {
        parent::__construct($transferId);
        $this->companyId = $companyId;
        $this->amount = $amount;
        $this->transferNo = $transferNo;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'  => $this->companyId,
            'amount'      => $this->amount,
            'transfer_no' => $this->transferNo,
        ]);
    }
}