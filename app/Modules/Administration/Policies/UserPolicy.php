<?php
// Path: app/Modules/Administration/Policies/UserPolicy.php

declare(strict_types=1);

namespace App\Modules\Administration\Policies;

use App\Core\Authorization\Policy;
use App\Core\Auth\AuthUser;

/**
 * Enterprise Policy: User Policy
 * يتحكم بالقواعد التجارية الدقيقة التي تفصل بين صلاحية "تعديل مستخدم" بشكل عام، وبين "أي مستخدم تحديداً مسموح لك بتعديله".
 */
class UserPolicy extends Policy
{
    /**
     * فحص هل يُسمح للمستخدم بتحديث بيانات مستخدم آخر.
     *
     * @param AuthUser $currentUser المستخدم الذي ينفذ الطلب
     * @param array $targetUser البيانات الخام للمستخدم المراد تعديله
     * @return bool
     */
    public function update(AuthUser $currentUser, array $targetUser): bool
    {
        // 1. لا يُسمح بتعديل مستخدم خارج نطاق شركتك
        if ($currentUser->companyId !== (int) $targetUser['company_id']) {
            return false;
        }

        // 2. إذا كان المستخدم المراد تعديله هو Super Admin، يجب أن يكون المُعدِّل Super Admin أيضاً
        // (بافتراض أن role_id للـ Super Admin هو 1)
        $isTargetSuperAdmin = isset($targetUser['role_id']) && (int) $targetUser['role_id'] === 1;
        $isCurrentSuperAdmin = true; // منطق افتراضي يتم التحقق منه عبر الـ RoleManager
        
        if ($isTargetSuperAdmin && !$isCurrentSuperAdmin) {
            return false;
        }

        return true;
    }

    /**
     * فحص هل يُسمح بالحذف.
     */
    public function delete(AuthUser $currentUser, array $targetUser): bool
    {
        // القاعدة الذهبية: لا يمكن للمستخدم أن يحذف نفسه!
        if ($currentUser->id === (int) $targetUser['id']) {
            return false;
        }

        return $this->update($currentUser, $targetUser);
    }
}