<?php
// Path: app/Modules/Treasury/Exceptions/InsufficientFundsException.php

declare(strict_types=1);

namespace App\Modules\Treasury\Exceptions;

use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Exception: Insufficient Funds
 * يُرمى عندما لا تمتلك الخزينة أو الحساب البنكي رصيداً كافياً لإتمام عملية (صرف أو تحويل).
 */
class InsufficientFundsException extends BusinessException
{
    public function __construct(string $accountName, float $requested, float $available)
    {
        $message = "Treasury Rule Violation: Insufficient funds in account [{$accountName}]. " .
                   "Requested: {$requested}, Available: {$available}.";
                   
        parent::__construct($message, 422);
    }
}