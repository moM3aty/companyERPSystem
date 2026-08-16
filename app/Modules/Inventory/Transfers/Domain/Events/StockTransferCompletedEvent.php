<?php
// Path: app/Modules/Inventory/Transfers/Domain/Events/StockTransferCompletedEvent.php

declare(strict_types=1);

namespace App\Modules\Inventory\Transfers\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Stock Transfer Completed
 */
class StockTransferCompletedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $transferId;

    public function __construct(int $transferId, int $companyId)
    {
        parent::__construct($transferId);
        $this->transferId = $transferId;
        $this->companyId = $companyId;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'  => $this->companyId,
            'transfer_id' => $this->transferId,
        ]);
    }
}