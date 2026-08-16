<?php
// Path: app/Core/Files/File.php

declare(strict_types=1);

namespace App\Core\Files;

use App\Core\Models\Entity;

/**
 * Enterprise File Entity
 * يمثل سجل لملف تم رفعه إلى النظام (البيانات الوصفية للملف وليس الملف الفيزيائي نفسه).
 */
class File extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'company_id' => 'integer',
        'disk' => 'string', // 'local', 's3'
        'path' => 'string', // المسار النسبي
        'original_name' => 'string',
        'mime_type' => 'string',
        'size_bytes' => 'integer',
        'uploaded_by' => 'integer',
        'created_at' => 'string',
    ];

    /**
     * استخراج امتداد الملف من اسمه الأصلي.
     *
     * @return string
     */
    public function getExtension(): string
    {
        return pathinfo((string)$this->getAttribute('original_name'), PATHINFO_EXTENSION);
    }
}