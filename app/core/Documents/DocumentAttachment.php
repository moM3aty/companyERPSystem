<?php
// Path: app/Core/Documents/DocumentAttachment.php

declare(strict_types=1);

namespace App\Core\Documents;

use App\Core\Models\Entity;

/**
 * Enterprise Document Attachment Entity
 * يمثل المرفقات (صور، PDF) المرتبطة بمستند معين.
 */
class DocumentAttachment extends Entity
{
    protected array $casts = [
        'id'            => 'integer',
        'document_id'   => 'integer',
        'file_path'     => 'string',
        'file_name'     => 'string',
        'mime_type'     => 'string',
        'file_size'     => 'integer',
        'uploaded_by'   => 'integer',
        'uploaded_at'   => 'string',
    ];
}