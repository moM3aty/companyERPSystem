<?php
// Path: app/Core/Documents/DocumentReference.php

declare(strict_types=1);

namespace App\Core\Documents;

use App\Core\Models\Entity;

/**
 * Enterprise Document Reference Entity
 * يمثل شجرة العلاقات بين المستندات (مثال: الفاتورة رقم 10 مرتبطة بأمر تسليم رقم 5).
 */
class DocumentReference extends Entity
{
    protected array $casts = [
        'id'                     => 'integer',
        'source_document_id'     => 'integer', // المستند الأصل (مثال: أمر الشراء)
        'target_document_id'     => 'integer', // المستند الفرع (مثال: فاتورة المشتريات)
        'reference_type'         => 'string',  // 'generated_from', 'related_to'
        'created_at'             => 'string',
    ];
}