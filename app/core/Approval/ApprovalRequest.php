<?php
// Path: app/Core/Approval/ApprovalRequest.php

declare(strict_types=1);

namespace App\Core\Approval;

use App\Core\Models\Entity;

/**
 * Enterprise Approval Request Entity
 * يمثل طلب الموافقة الرئيسي المربوط بمستند معين (مثل فاتورة، طلب إجازة).
 */
class ApprovalRequest extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'company_id' => 'integer',
        'document_type' => 'string', // e.g., 'purchase_order', 'sales_invoice'
        'document_id' => 'integer',
        'requester_id' => 'integer',
        'status' => 'string', // 'pending', 'approved', 'rejected', 'cancelled'
    ];

    /**
     * التحقق مما إذا كان الطلب قيد الانتظار.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->getAttribute('status') === 'pending';
    }
}