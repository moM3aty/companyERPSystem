<?php
// Path: app/Domain/Enums/DocumentStatus.php

declare(strict_types=1);

namespace App\Domain\Enums;

/**
 * Enterprise Enum: Document Status (Requires PHP 8.1+)
 * توحيد حالات المستندات في النظام بأكمله لضمان عدم وجود أخطاء إملائية (Magic Strings).
 */
enum DocumentStatus: string
{
    case DRAFT = 'draft';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case POSTED = 'posted';
    case CANCELLED = 'cancelled';
    case VOIDED = 'voided';
}