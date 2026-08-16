<?php
// Path: app/Modules/Purchasing/Models/SupplierContact.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Model: Supplier Contact
 * يمثل جهات الاتصال المتعددة للمورد الواحد (مثل: مدير المبيعات لدى المورد، مسؤول التحصيل).
 */
class SupplierContact extends BaseModel
{
    use HasTimestamps;

    protected array $casts = [
        'id'          => 'integer',
        'supplier_id' => 'integer',
        'name'        => 'string',
        'job_title'   => 'string',
        'email'       => 'string',
        'phone'       => 'string',
        'is_primary'  => 'boolean',
        'created_at'  => 'string',
        'updated_at'  => 'string',
    ];
}