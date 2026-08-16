<?php
// Path: app/Core/Documents/DocumentVersion.php

declare(strict_types=1);

namespace App\Core\Documents;

use App\Core\Models\Entity;

/**
 * Enterprise Document Version Entity
 * يمثل لقطة (Snapshot) لبيانات المستند في وقت معين لضمان تتبع التعديلات (Audit Trail).
 */
class DocumentVersion extends Entity
{
    protected array $casts = [
        'id'              => 'integer',
        'document_id'     => 'integer',
        'version_number'  => 'integer',
        'payload'         => 'json', // يتم حفظ محتوى المستند بالكامل كـ JSON
        'changed_by'      => 'integer',
        'change_reason'   => 'string',
        'created_at'      => 'string',
    ];
}