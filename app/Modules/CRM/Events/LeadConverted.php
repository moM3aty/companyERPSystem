<?php
// Path: app/Modules/CRM/Events/LeadConverted.php

declare(strict_types=1);

namespace App\Modules\CRM\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Lead Converted
 * يُطلق بعد أن يقوم مندوب المبيعات بتحويل الـ Lead إلى Customer و Opportunity ناجح.
 */
class LeadConverted extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $newCustomerId;
    public readonly int $opportunityId;

    public function __construct(int $leadId, int $companyId, int $newCustomerId, int $opportunityId)
    {
        parent::__construct($leadId);
        $this->companyId = $companyId;
        $this->newCustomerId = $newCustomerId;
        $this->opportunityId = $opportunityId;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'     => $this->companyId,
            'new_customer_id'=> $this->newCustomerId,
            'opportunity_id' => $this->opportunityId,
        ]);
    }
}