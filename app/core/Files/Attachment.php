<?php
// Path: app/Core/Files/Attachment.php

declare(strict_types=1);

namespace App\Core\Files;

use App\Core\Models\Entity;

/**
 * Enterprise Attachment Entity
 * يمثل علاقة ربط (Polymorphic) بين ملف وأي كيان آخر في النظام (مثال: صورة مرفقة بمنتج، أو PDF مرفق بفاتورة).
 */
class Attachment extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'file_id' => 'integer',
        'attachable_type' => 'string', // e.g., 'sales_invoices', 'products'
        'attachable_id' => 'integer',
        'collection_name' => 'string', // e.g., 'profile_picture', 'receipts'
        'created_at' => 'string',
    ];
}