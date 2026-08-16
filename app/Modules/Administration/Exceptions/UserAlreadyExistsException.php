<?php
// Path: app/Modules/Administration/Exceptions/UserAlreadyExistsException.php

declare(strict_types=1);

namespace App\Modules\Administration\Exceptions;

use App\Domain\Exceptions\BusinessRuleViolationException;

/**
 * Enterprise Domain Exception: User Already Exists
 * يُرمى عندما يحاول مدير إنشاء مستخدم ببريد إلكتروني موجود مسبقاً داخل نطاق شركته.
 */
class UserAlreadyExistsException extends BusinessRuleViolationException
{
    public function __construct(string $email, int $companyId)
    {
        $message = "A user with the email address [{$email}] is already registered in company [{$companyId}].";
        
        parent::__construct($message);
    }
}