<?php
// Path: app/Modules/POS/Terminals/Domain/PosTerminal.php

declare(strict_types=1);

namespace App\Modules\POS\Terminals\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasSoftDeletes;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: POS Terminal
 * يمثل نقطة البيع (الكاشير) ككيان مادي داخل الفرع.
 */
class PosTerminal extends BaseModel
{
    use HasTenant, HasTimestamps, HasSoftDeletes, HasAudit;

    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'branch_id'   => 'integer',
        'name'        => 'string',
        'code'        => 'string', // e.g., 'TERM-01'
        'is_active'   => 'boolean',
        'created_at'  => 'string',
        'updated_at'  => 'string',
        'deleted_at'  => 'string',
    ];
}