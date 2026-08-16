<?php
// Path: app/Domain/Enums/RecordStatus.php

declare(strict_types=1);

namespace App\Domain\Enums;

/**
 * Enterprise Domain Enum: Record Status
 * يوحد الحالات القياسية للسجلات في قاعدة البيانات (مثل تفعيل العملاء أو المنتجات).
 */
enum RecordStatus: string
{
    case DRAFT    = 'draft';
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';
    case ARCHIVED = 'archived';
    case DELETED  = 'deleted';
}