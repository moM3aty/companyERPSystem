<?php
// Path: app/Core/Documents/Document.php

declare(strict_types=1);

namespace App\Core\Documents;

use App\Core\Models\Entity;

/**
 * Enterprise Document Entity
 * يمثل الترويسة الموحدة لأي مستند في النظام (فاتورة، عرض سعر، قيد يومية).
 */
class Document extends Entity
{
    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'branch_id'        => 'integer',
        'document_type_id' => 'integer',
        'document_number'  => 'string',
        'status'           => 'string', // draft, pending_approval, approved, posted, voided
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
        'locked_at'        => 'string',
    ];

    /**
     * التحقق مما إذا كان المستند مرحلاً (نهائياً ولا يمكن تعديله).
     *
     * @return bool
     */
    public function isPosted(): bool
    {
        return $this->getAttribute('status') === DocumentStatus::POSTED;
    }
}