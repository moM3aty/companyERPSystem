<?php
// Path: app/Modules/Inventory/Categories/Domain/CategoryRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Inventory\Categories\Domain;

use App\Core\Contracts\RepositoryInterface;

interface CategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * بناء شجرة التصنيفات للاستخدام في واجهات المستخدم (Dropdowns).
     *
     * @param int $companyId
     * @return array
     */
    public function getTree(int $companyId): array;
}