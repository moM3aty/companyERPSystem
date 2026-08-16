<?php
// Path: app/Modules/Purchasing/Exceptions/GoodsReceiptMismatchException.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Exceptions;

use App\Domain\Exceptions\BusinessRuleViolationException;

/**
 * Enterprise Domain Exception: Goods Receipt Mismatch
 * يُرمى عندما يحاول أمين المستودع استلام كمية أكبر من المذكورة في أمر الشراء 
 * بما يتجاوز نسبة التسامح (Tolerance Limit) المسموح بها.
 */
class GoodsReceiptMismatchException extends BusinessRuleViolationException
{
    public function __construct(string $productName, float $ordered, float $received, float $tolerancePercent)
    {
        $message = "GRN Rule Violation: Received quantity ({$received}) for product [{$productName}] exceeds the ordered quantity ({$ordered}) beyond the allowed tolerance limit ({$tolerancePercent}%).";
        parent::__construct($message);
    }
}