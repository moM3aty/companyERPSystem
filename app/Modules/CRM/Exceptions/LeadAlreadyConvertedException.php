<?php
// Path: app/Modules/CRM/Exceptions/LeadAlreadyConvertedException.php

declare(strict_types=1);

namespace App\Modules\CRM\Exceptions;

use App\Domain\Exceptions\BusinessRuleViolationException;

/**
 * Enterprise Domain Exception: Lead Already Converted
 * يُرمى عندما يحاول موظف المبيعات تعديل أو تحويل Lead تم تحويله مسبقاً لعميل أو فرصة.
 */
class LeadAlreadyConvertedException extends BusinessRuleViolationException
{
    public function __construct(int $leadId)
    {
        $message = "CRM Rule Violation: Lead [ID: {$leadId}] has already been converted into an Opportunity or Customer. No further modifications are allowed.";
        parent::__construct($message);
    }
}