<?php
// Path: app/Modules/POS/Models/POSClosing.php
declare(strict_types=1);

namespace App\Modules\POS\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: POS Closing (Z-Report)
 * تقرير إغلاق الصندوق النهائي.
 */
class POSClosing extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'              => 'integer',
        'company_id'      => 'integer',
        'terminal_id'     => 'integer',
        'shift_id'        => 'integer',
        'expected_amount' => 'float',
        'actual_amount'   => 'float',
        'difference'      => 'float',
        'closed_by'       => 'integer',
        'created_at'      => 'string',
        'updated_at'      => 'string',
    ];
}