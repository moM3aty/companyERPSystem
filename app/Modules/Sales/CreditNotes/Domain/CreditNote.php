<?php
// Path: app/Modules/Sales/CreditNotes/Domain/CreditNote.php

declare(strict_types=1);

namespace App\Modules\Sales\CreditNotes\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Credit Note
 * إشعار الدائن (المرتجعات). مستند مالي يثبت مديونيتنا للعميل نتيجة إرجاع بضاعة أو تخفيض فاتورة.
 */
class CreditNote extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'branch_id'        => 'integer',
        'credit_note_no'   => 'string',
        'invoice_id'       => 'integer', // الفاتورة المرتبطة بالمرتجع (اختياري في بعض الأنظمة لكن مفضل)
        'customer_id'      => 'integer',
        'note_date'        => 'string',
        'currency_id'      => 'integer',
        'subtotal'         => 'float',
        'tax_total'        => 'float',
        'grand_total'      => 'float',
        'reason'           => 'string',
        'status'           => 'string', // 'draft', 'posted', 'refunded'
        'journal_entry_id' => 'integer',
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}