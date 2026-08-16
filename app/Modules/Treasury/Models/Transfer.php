<?php
// Path: app/Modules/Treasury/Models/Transfer.php

declare(strict_types=1);

namespace App\Modules\Treasury\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Model: Treasury Transfer
 * يمثل حركة نقل أموال داخلية بين خزنتين أو حسابين بنكيين لنفس الشركة.
 */
class Transfer extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'transfer_no'      => 'string',
        'from_account_id'  => 'integer',
        'to_account_id'    => 'integer',
        'amount'           => 'float',
        'transfer_date'    => 'string',
        'reference'        => 'string',
        'description'      => 'string',
        'journal_entry_id' => 'integer',
        'status'           => 'string', // 'pending', 'completed', 'cancelled'
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}