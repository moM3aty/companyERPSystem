<?php
// Path: app/Core/Documents/DocumentMetadata.php

declare(strict_types=1);

namespace App\Core\Documents;

use App\Core\Models\Entity;

/**
 * Enterprise Document Metadata Entity
 * يتيح إضافة حقول ديناميكية (Key-Value) لأي مستند دون الحاجة لتعديل هيكل قاعدة البيانات.
 */
class DocumentMetadata extends Entity
{
    protected array $casts = [
        'id'          => 'integer',
        'document_id' => 'integer',
        'meta_key'    => 'string',
        'meta_value'  => 'string', // القيمة كـ String ويمكن أن تكون JSON
    ];
}