<?php
// Path: app/Modules/Administration/Exceptions/InvalidRoleException.php

declare(strict_types=1);

namespace App\Modules\Administration\Exceptions;

use App\Domain\Exceptions\BusinessRuleViolationException;

/**
 * Enterprise Domain Exception: Invalid Role
 * يُرمى عند محاولة ربط مستخدم بصلاحية/دور غير موجود أو لا ينتمي لشركته.
 */
class InvalidRoleException extends BusinessRuleViolationException
{
    public function __construct(int $roleId)
    {
        $message = "The assigned role ID [{$roleId}] is invalid, inactive, or belongs to another tenant.";
        
        parent::__construct($message);
    }
}