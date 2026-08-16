<?php
// Path: app/Core/Documents/DocumentStatus.php

declare(strict_types=1);

namespace App\Core\Documents;

/**
 * Enterprise Document Status Dictionary
 * يوحد حالات المستندات لمنع استخدام نصوص سحرية (Magic Strings) في الكود.
 */
class DocumentStatus
{
    public const DRAFT = 'draft';
    public const PENDING_APPROVAL = 'pending_approval';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';
    public const POSTED = 'posted';
    public const CANCELLED = 'cancelled';
    public const VOIDED = 'voided';

    /**
     * جلب جميع الحالات المتاحة.
     *
     * @return array
     */
    public static function all(): array
    {
        return [
            self::DRAFT,
            self::PENDING_APPROVAL,
            self::APPROVED,
            self::REJECTED,
            self::POSTED,
            self::CANCELLED,
            self::VOIDED,
        ];
    }
}