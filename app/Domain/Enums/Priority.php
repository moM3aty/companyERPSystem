<?php
// Path: app/Domain/Enums/Priority.php

declare(strict_types=1);

namespace App\Domain\Enums;

/**
 * Enterprise Domain Enum: Priority
 * يوحد مستويات الأولوية في مهام الـ (Projects) وأوامر العمل (Maintenance).
 */
enum Priority: string
{
    case LOW      = 'low';
    case NORMAL   = 'normal';
    case HIGH     = 'high';
    case URGENT   = 'urgent';
    case CRITICAL = 'critical';
}