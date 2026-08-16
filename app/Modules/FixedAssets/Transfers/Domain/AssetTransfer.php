<?php
// Path: app/Modules/FixedAssets/Transfers/Domain/AssetTransfer.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Transfers\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Asset Transfer
 * يمثل حركة نقل أصل من فرع/موقع إلى آخر (العهدة).
 */
class AssetTransfer extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'asset_id'         => 'integer',
        'from_branch_id'   => 'integer',
        'to_branch_id'     => 'integer',
        'from_location_id' => 'integer',
        'to_location_id'   => 'integer',
        'transfer_date'    => 'string',
        'notes'            => 'string',
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}