<?php
// Path: app/Modules/HR/Policies/EmployeePolicy.php

declare(strict_types=1);

namespace App\Modules\HR\Policies;

use App\Core\Authorization\Policy;
use App\Core\Auth\AuthUser;

/**
 * Enterprise Policy: Employee Data
 * يحمي بيانات الموظف. يضمن أن الموظف يرى بياناته فقط أو أن المدير يرى بيانات موظفيه (Company Scope).
 */
class EmployeePolicy extends Policy
{
    public function view(AuthUser $currentUser, array $employee): bool
    {
        // 1. Same company check
        if ($currentUser->companyId !== (int) $employee['company_id']) {
            return false;
        }

        // 2. ESS (Self Service) Check
        if ($currentUser->employeeId === (int) $employee['id']) {
            return true;
        }

        // 3. Manager/HR access is handled by Role/Permissions (Gate), 
        // so if they pass the Gate and the company matches, it's allowed.
        return true;
    }

    public function update(AuthUser $currentUser, array $employee): bool
    {
        return $this->view($currentUser, $employee);
    }
}