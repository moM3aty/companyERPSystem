<?php
// Path: app/Modules/Administration/Roles/Domain/RoleRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Administration\Roles\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Role
 */
interface RoleRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب دور بناءً على الاسم داخل الشركة.
     *
     * @param string $name
     * @param int $companyId
     * @return array|null
     */
    public function findByName(string $name, int $companyId): ?array;
}