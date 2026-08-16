<?php
// Path: app/Domain/Exceptions/BusinessRuleViolationException.php

declare(strict_types=1);

namespace App\Domain\Exceptions;

/**
 * Enterprise Exception: Business Rule Violation
 * يُرمى عند الإخلال بقاعدة عمل (مثال: محاولة إضافة كمية سالبة، أو عملة غير متطابقة).
 */
class BusinessRuleViolationException extends DomainException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 422);
    }
}