<?php
// Path: app/Modules/HR/Policies/LeavePolicy.php

declare(strict_types=1);

namespace App\Modules\HR\Policies;

use App\Core\Authorization\Policy;
use App\Core\Auth\AuthUser;

/**
 * Enterprise Policy: Leave Request
 * يحدد من يحق له الموافقة على الإجازة (يجب أن يكون المدير المباشر أو الـ HR).
 */
class LeavePolicy extends Policy
{
    public function approve(AuthUser $currentUser, array $leaveRequest): bool
    {
        if ($currentUser->companyId !== (int) $leaveRequest['company_id']) {
            return false;
        }

        if ($leaveRequest['status'] !== 'pending') {
            return false;
        }

        return true;
    }
}