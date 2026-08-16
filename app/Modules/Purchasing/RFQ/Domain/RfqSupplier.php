<?php
// Path: app/Modules/Purchasing/RFQ/Domain/RfqSupplier.php

declare(strict_types=1);

namespace App\Modules\Purchasing\RFQ\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: RFQ Supplier
 * يمثل الموردين المدعوين لتقديم عروض أسعار على هذا الـ RFQ.
 */
class RfqSupplier extends Entity
{
    protected array $casts = [
        'id'          => 'integer',
        'rfq_id'      => 'integer',
        'supplier_id' => 'integer',
        'has_bid'     => 'boolean', // هل قام المورد بالرد وتقديم عرض سعر؟
        'is_winner'   => 'boolean', // هل تم اختياره لترسية العطاء عليه؟
    ];
}