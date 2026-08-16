<?php
// Path: app/Modules/Maintenance/SpareParts/Domain/SparePart.php

declare(strict_types=1);

namespace App\Modules\Maintenance\SpareParts\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Spare Part
 * يمثل قطعة غيار مستخدمة في أوامر عمل الصيانة (قد تكون مربوطة بمنتج في المخازن الرئيسية).
 */
class SparePart extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'part_number'    => 'string',
        'name'           => 'string',
        'product_id'     => 'integer', // الارتباط بموديول المخازن (Inventory)
        'stock_quantity' => 'float',   // الرصيد المتاح داخل ورشة الصيانة
        'unit_cost'      => 'float',   // التكلفة لحساب إجمالي تكلفة أمر العمل
        'is_active'      => 'boolean',
        'created_at'     => 'string',
        'updated_at'     => 'string',
    ];
}