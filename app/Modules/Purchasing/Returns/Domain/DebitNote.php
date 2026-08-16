<?php
// Path: app/Modules/Purchasing/Returns/Domain/DebitNote.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Returns\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Debit Note (Purchase Return)
 * إشعار مدين للمورد. يثبت أننا أرجعنا بضاعة تالفة، وبالتالي انخفضت مديونيتنا له.
 */
class DebitNote extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                  => 'integer',
        'company_id'          => 'integer',
        'branch_id'           => 'integer',
        'debit_note_no'       => 'string',
        'purchase_invoice_id' => 'integer', // الفاتورة الأصلية المرتبطة
        'supplier_id'         => 'integer',
        'note_date'           => 'string',
        'currency_id'         => 'integer',
        'subtotal'            => 'float',
        'tax_total'           => 'float',
        'grand_total'         => 'float',
        'reason'              => 'string',
        'status'              => 'string', // 'draft', 'posted', 'refunded'
        'journal_entry_id'    => 'integer', // القيد المعكوس (COGS/Inventory -> AP)
        'created_by'          => 'integer',
        'created_at'          => 'string',
        'updated_at'          => 'string',
    ];
}