<?php
// Path: app/Modules/Projects/Projects/Domain/ProjectRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Projects\Projects\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Project
 */
interface ProjectRepositoryInterface extends RepositoryInterface
{
    /**
     * توليد كود متسلسل للمشروع داخل الشركة (مثال: PRJ-0001).
     *
     * @param int $companyId
     * @return string
     */
    public function generateProjectCode(int $companyId): string;
}