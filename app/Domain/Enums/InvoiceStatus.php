<?php
// Path: app/Domain/Enums/InvoiceStatus.php

declare(strict_types=1);

namespace App\Domain\Enums;

/**
 * Enterprise Domain Enum: Invoice Status
 * يمنع استخدام نصوص ثابتة (Magic Strings) للحالات الحساسة كالمدفوعات والمطالبات.
 */
enum InvoiceStatus: string
{
    case DRAFT          = 'draft';
    case SENT           = 'sent';
    case VIEWED         = 'viewed';
    case PARTIALLY_PAID = 'partially_paid';
    case PAID           = 'paid';
    case OVERDUE        = 'overdue';
    case CANCELLED      = 'cancelled';
    case VOIDED         = 'voided';
}