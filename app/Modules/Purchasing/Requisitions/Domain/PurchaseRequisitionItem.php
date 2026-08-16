<?php
// Path: app/Modules/Purchasing/Requisitions/Domain/PurchaseRequisitionItem.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Requisitions\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Purchase Requisition Item
 * تفاصيل الأصناف المطلوبة داخل طلب الاحتياج.
 */
class PurchaseRequisitionItem extends Entity
{
    protected array $casts = [
        'id'                      => 'integer',
        'purchase_requisition_id' => 'integer',
        'product_id'              => 'integer',
        'description'             => 'string',
        'quantity'                => 'float',
        'estimated_unit_price'    => 'float', // سعر تقديري يضعه الطالب أو يسحب من التكلفة الحالية
        'total_estimated'         => 'float',
    ];
}