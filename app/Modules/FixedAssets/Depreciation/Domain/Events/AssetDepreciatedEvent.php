<?php
// Path: app/Modules/FixedAssets/Depreciation/Domain/Events/AssetDepreciatedEvent.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Depreciation\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Asset Depreciated
 * يُطلق هذا الحدث بمجرد حساب وحفظ الإهلاك، ليقوم محرك المحاسبة (Accounting Engine) 
 * بالاستماع له وتوليد قيد الإهلاك آلياً (Depreciation Journal Entry).
 */
class AssetDepreciatedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $assetId;
    public readonly float $depreciationAmount;
    public readonly int $recordId;

    public function __construct(int $recordId, int $companyId, int $assetId, float $depreciationAmount)
    {
        parent::__construct($recordId);
        $this->recordId = $recordId;
        $this->companyId = $companyId;
        $this->assetId = $assetId;
        $this->depreciationAmount = $depreciationAmount;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'          => $this->companyId,
            'asset_id'            => $this->assetId,
            'depreciation_amount' => $this->depreciationAmount,
            'record_id'           => $this->recordId,
        ]);
    }
}