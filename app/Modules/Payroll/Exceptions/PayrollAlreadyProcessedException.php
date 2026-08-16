<?php
// Path: app/Modules/Payroll/Exceptions/PayrollAlreadyProcessedException.php

declare(strict_types=1);

namespace App\Modules\Payroll\Exceptions;

use App\Domain\Exceptions\BusinessRuleViolationException;

/**
 * Enterprise Domain Exception: Payroll Already Processed
 * يحمي النظام المحاسبي من دبلجة (Duplication) رواتب الموظفين في نفس الشهر.
 */
class PayrollAlreadyProcessedException extends BusinessRuleViolationException
{
    public function __construct(string $period)
    {
        $message = "Payroll Protection Rule: The payroll for the period [{$period}] has already been processed and locked. Modifications are strictly prohibited.";
        
        parent::__construct($message);
    }
}