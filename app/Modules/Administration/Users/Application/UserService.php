<?php
// Path: app/Modules/Administration/Users/Application/UserService.php

declare(strict_types=1);

namespace App\Modules\Administration\Users\Application;

use App\Modules\Administration\Users\Domain\User;
use App\Modules\Administration\Users\Domain\UserRepositoryInterface;
use App\Modules\Administration\Users\Domain\Events\UserCreatedEvent;
use App\Core\Security\HashManager;
use App\Core\Database\TransactionManager;
use App\Core\Events\EventBus;
use App\Core\Authorization\RoleManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: User Management
 * يحتوي على الـ Business Logic الخاص بالمستخدمين. يجمع بين الـ Repo, EventBus, و الـ Transaction.
 */
class UserService
{
    protected UserRepositoryInterface $userRepo;
    protected HashManager $hashManager;
    protected TransactionManager $transaction;
    protected EventBus $eventBus;
    protected RoleManager $roleManager;

    public function __construct(
        UserRepositoryInterface $userRepo,
        HashManager $hashManager,
        TransactionManager $transaction,
        EventBus $eventBus,
        RoleManager $roleManager
    ) {
        $this->userRepo = $userRepo;
        $this->hashManager = $hashManager;
        $this->transaction = $transaction;
        $this->eventBus = $eventBus;
        $this->roleManager = $roleManager;
    }

    /**
     * إنشاء مستخدم جديد بشكل متكامل ومحمي بـ Transaction.
     *
     * @param array $data البيانات الموثقة من הـ Request
     * @param int $companyId
     * @return User
     * @throws BusinessException|\Throwable
     */
    public function createUser(array $data, int $companyId): User
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            
            // 1. Prepare data
            $data['company_id'] = $companyId;
            $data['password_hash'] = $this->hashManager->make($data['password']);
            $data['is_active'] = 1;
            
            unset($data['password']);
            
            $roleIds = $data['role_ids'] ?? [];
            unset($data['role_ids']);

            // 2. Persist to DB
            $userId = $this->userRepo->create($data);
            
            /** @var User $user */
            $user = $this->userRepo->findEntity($userId);

            // 3. Assign Roles
            foreach ($roleIds as $roleId) {
                $this->roleManager->assignRoleToUser($userId, (int) $roleId);
            }

            // 4. Publish Domain Event (To notify Welcome Email listener, Audit, etc.)
            $this->eventBus->publish(new UserCreatedEvent(
                $userId, 
                $companyId, 
                (string) $user->email, 
                (string) $user->username
            ));

            return $user;
        });
    }

    /**
     * تحديث بيانات المستخدم.
     *
     * @param int $userId
     * @param array $data
     * @param int $companyId
     * @return User
     * @throws \Throwable
     */
    public function updateUser(int $userId, array $data, int $companyId): User
    {
        $this->userRepo->setTenantId($companyId);
        $userArray = $this->userRepo->findOrFail($userId);

        return $this->transaction->execute(function () use ($userId, $data, $companyId) {
            
            $roleIds = $data['role_ids'] ?? null;
            unset($data['role_ids']);

            if (!empty($data)) {
                $this->userRepo->update($userId, $data);
            }

            if ($roleIds !== null) {
                // In a real scenario, you'd sync roles here (delete old, insert new)
                // $this->roleManager->syncUserRoles($userId, $roleIds);
            }

            /** @var User $updatedUser */
            $updatedUser = $this->userRepo->findEntity($userId);
            
            return $updatedUser;
        });
    }
}