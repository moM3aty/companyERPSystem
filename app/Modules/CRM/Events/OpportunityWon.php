<?php
// Path: app/Modules/CRM/Events/OpportunityWon.php

declare(strict_types=1);

namespace App\Modules\CRM\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Opportunity Won
 * يُطلق عند الفوز بفرصة بيعية لتنبيه فريق الإدارة وإضافة التارجت لمندوب المبيعات.
 */
class OpportunityWon extends DomainEvent
{
    public readonly int $companyId;
    public readonly float $revenue;
    public readonly int $assignedTo;

    public function __construct(int $opportunityId, int $companyId, float $revenue, int $assignedTo)
    {
        parent::__construct($opportunityId);
        $this->companyId = $companyId;
        $this->revenue = $revenue;
        $this->assignedTo = $assignedTo;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'  => $this->companyId,
            'revenue'     => $this->revenue,
            'assigned_to' => $this->assignedTo,
        ]);
    }
}