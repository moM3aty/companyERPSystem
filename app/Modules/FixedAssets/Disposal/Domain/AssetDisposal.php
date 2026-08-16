<?php
// Path: app/Modules/FixedAssets/Disposal/Domain/AssetDisposal.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Disposal\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Asset Disposal
 * يمثل عملية استبعاد الأصل (بالبيع أو التكهين/الإتلاف) وتسوية قيمته الدفترية.
 */
class AssetDisposal extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'asset_id'         => 'integer',
        'disposal_date'    => 'string', // YYYY-MM-DD
        'disposal_type'    => 'string', // 'sold', 'scrapped'
        'sale_amount'      => 'float',  // 0.0 in case of scrapped
        'net_book_value'   => 'float',  // القيمة الدفترية وقت الاستبعاد
        'gain_loss_amount' => 'float',  // موجب = ربح، سالب = خسارة
        'journal_entry_id' => 'integer', // القيد المولد آلياً لإقفال الأصل
        'reason'           => 'string',
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}