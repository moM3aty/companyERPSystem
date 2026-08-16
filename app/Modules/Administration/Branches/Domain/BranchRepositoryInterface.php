<?php
// Path: app/Modules/Administration/Branches/Domain/BranchRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Administration\Branches\Domain;

use App\Core\Contracts\RepositoryInterface;

interface BranchRepositoryInterface extends RepositoryInterface
{
    /**
     * التحقق من كود الفرع داخل نفس الشركة.
     *
     * @param string $code
     * @param int $companyId
     * @return bool
     */
    public function codeExists(string $code, int $companyId): bool;
}