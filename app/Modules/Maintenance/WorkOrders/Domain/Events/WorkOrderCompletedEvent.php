<?php
// Path: app/Modules/Maintenance/WorkOrders/Domain/Events/WorkOrderCompletedEvent.php

declare(strict_types=1);

namespace App\Modules\Maintenance\WorkOrders\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Work Order Completed
 * يُطلق عند انتهاء أمر العمل.
 * يستخدم لإغلاق طلبات قطع الغيار في المخازن، أو تسجيل التكلفة في الأصول والمحاسبة.
 */
class WorkOrderCompletedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $assetId;
    public readonly float $actualCost;

    public function __construct(int $workOrderId, int $companyId, int $assetId, float $actualCost)
    {
        parent::__construct($workOrderId);
        $this->companyId = $companyId;
        $this->assetId = $assetId;
        $this->actualCost = $actualCost;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'  => $this->companyId,
            'asset_id'    => $this->assetId,
            'actual_cost' => $this->actualCost,
        ]);
    }
}