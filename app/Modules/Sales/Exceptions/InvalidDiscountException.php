<?php
// Path: app/Modules/Sales/Exceptions/InvalidDiscountException.php

declare(strict_types=1);

namespace App\Modules\Sales\Exceptions;

use App\Domain\Exceptions\BusinessRuleViolationException;

/**
 * Enterprise Domain Exception: Invalid Discount
 * يُرمى عندما يحاول موظف المبيعات إدخال خصم يتجاوز إجمالي الفاتورة أو يتجاوز الصلاحيات الممنوحة له.
 */
class InvalidDiscountException extends BusinessRuleViolationException
{
    public function __construct(float $discount, float $maxAllowed)
    {
        $message = "Sales Rule Violation: The requested discount ({$discount}) exceeds the maximum allowed limit ({$maxAllowed}).";
        parent::__construct($message);
    }
}