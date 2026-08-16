<?php
// Path: app/Modules/Administration/Companies/Domain/CompanyRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Administration\Companies\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Company
 */
interface CompanyRepositoryInterface extends RepositoryInterface
{
    /**
     * التحقق مما إذا كان رقم التسجيل الضريبي أو السجل التجاري موجوداً مسبقاً.
     *
     * @param string $registrationNumber
     * @return bool
     */
    public function registrationExists(string $registrationNumber): bool;
}