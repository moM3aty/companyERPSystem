<?php
// Path: app/Modules/Administration/Users/Domain/UserRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Administration\Users\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: User
 * العقد الذي يحدد العمليات المسموحة لمستودع المستخدمين.
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * البحث عن مستخدم بواسطة البريد الإلكتروني.
     *
     * @param string $email
     * @param int $companyId
     * @return User|null
     */
    public function findByEmail(string $email, int $companyId): ?User;

    /**
     * تحديث حالة التفعيل للمستخدم.
     *
     * @param int $userId
     * @param bool $isActive
     * @return int
     */
    public function updateStatus(int $userId, bool $isActive): int;

    /**
     * إرجاع كائن User بدلاً من مصفوفة.
     *
     * @param int $id
     * @return User|null
     */
    public function findEntity(int $id): ?User;
}