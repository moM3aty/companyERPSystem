<?php
// Path: app/Modules/Administration/Roles/Application/RoleService.php

declare(strict_types=1);

namespace App\Modules\Administration\Roles\Application;

use App\Modules\Administration\Roles\Domain\RoleRepositoryInterface;
use App\Core\Authorization\RoleManager;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Role Management
 */
class RoleService
{
    protected RoleRepositoryInterface $roleRepo;
    protected RoleManager $roleManager;
    protected TransactionManager $transaction;

    public function __construct(
        RoleRepositoryInterface $roleRepo,
        RoleManager $roleManager,
        TransactionManager $transaction
    ) {
        $this->roleRepo = $roleRepo;
        $this->roleManager = $roleManager;
        $this->transaction = $transaction;
    }

    /**
     * إنشاء دور جديد وربطه بصلاحيات محددة.
     *
     * @param string $name
     * @param string $description
     * @param array $permissionIds
     * @param int $companyId
     * @return array
     * @throws BusinessException|\Throwable
     */
    public function createRoleWithPermissions(string $name, string $description, array $permissionIds, int $companyId): array
    {
        return $this->transaction->execute(function () use ($name, $description, $permissionIds, $companyId) {
            
            // Validate Uniqueness
            if ($this->roleRepo->findByName($name, $companyId)) {
                throw new BusinessException("A role with the name '{$name}' already exists in this company.");
            }

            // Create Role via Core Manager
            $roleId = $this->roleManager->createRole($name, $description);

            // Assign Permissions
            foreach ($permissionIds as $permissionId) {
                $this->roleManager->assignPermissionToRole($roleId, (int) $permissionId);
            }

            $this->roleRepo->setTenantId($companyId);
            return $this->roleRepo->findOrFail($roleId);
        });
    }
}