<?php
// Path: app/Modules/Sales/CreditNotes/Domain/CreditNoteItem.php

declare(strict_types=1);

namespace App\Modules\Sales\CreditNotes\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Credit Note Item
 * الأصناف المرتجعة داخل إشعار الدائن.
 */
class CreditNoteItem extends Entity
{
    protected array $casts = [
        'id'             => 'integer',
        'credit_note_id' => 'integer',
        'product_id'     => 'integer',
        'quantity'       => 'float',
        'unit_price'     => 'float',
        'tax_amount'     => 'float',
        'total'          => 'float',
        'warehouse_id'   => 'integer', // المستودع الذي استلم البضاعة المرتجعة
    ];
}