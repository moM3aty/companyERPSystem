<?php
// Path: app/Core/Documents/DocumentType.php

declare(strict_types=1);

namespace App\Core\Documents;

use App\Core\Models\Entity;

/**
 * Enterprise Document Type Entity
 * يمثل أنواع المستندات المدعومة في النظام وإعداداتها (هل تدعم المرفقات؟ هل تتطلب موافقة؟).
 */
class DocumentType extends Entity
{
    protected array $casts = [
        'id'                   => 'integer',
        'code'                 => 'string', // e.g., 'SALES_INVOICE', 'PURCHASE_ORDER'
        'name'                 => 'string',
        'requires_approval'    => 'boolean',
        'allows_attachments'   => 'boolean',
        'is_active'            => 'boolean',
    ];
}