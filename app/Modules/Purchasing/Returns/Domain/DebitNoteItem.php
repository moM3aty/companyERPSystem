<?php
// Path: app/Modules/Purchasing/Returns/Domain/DebitNoteItem.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Returns\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Debit Note Item
 * الأصناف المرجعة للمورد.
 */
class DebitNoteItem extends Entity
{
    protected array $casts = [
        'id'             => 'integer',
        'debit_note_id'  => 'integer',
        'product_id'     => 'integer',
        'warehouse_id'   => 'integer', // المستودع الذي سُحبت منه البضاعة المرتجعة
        'quantity'       => 'float',
        'unit_price'     => 'float',
        'tax_amount'     => 'float',
        'total'          => 'float',
    ];
}