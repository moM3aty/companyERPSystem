<?php
// Path: app/Modules/Assets/Models/AssetAcquisition.php
declare(strict_types=1);

namespace App\Modules\Assets\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Asset Acquisition
 * يوثق عملية الاستحواذ على أصل جديد (الرسملة) وربطه بمورد أو فاتورة مشتريات.
 */
class AssetAcquisition extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'asset_id'         => 'integer',
        'supplier_id'      => 'integer',
        'po_number'        => 'string',
        'invoice_number'   => 'string',
        'acquisition_cost' => 'float',
        'acquisition_date' => 'string',
        'journal_entry_id' => 'integer',
        'created_at'       => 'string',
    ];
}