<?php
// Path: app/Modules/Purchasing/Exceptions/SupplierNotActiveException.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Exceptions;

use App\Domain\Exceptions\BusinessRuleViolationException;

/**
 * Enterprise Domain Exception: Supplier Not Active
 * يمنع إنشاء أوامر شراء أو فواتير لمورد تم إيقاف التعامل معه.
 */
class SupplierNotActiveException extends BusinessRuleViolationException
{
    public function __construct(int $supplierId)
    {
        $message = "Procurement Rule Violation: Supplier [ID: {$supplierId}] is marked as inactive or blocked. Cannot proceed with the transaction.";
        parent::__construct($message);
    }
}