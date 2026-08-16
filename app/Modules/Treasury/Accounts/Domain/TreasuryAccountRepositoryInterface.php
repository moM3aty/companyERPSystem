<?php
// Path: app/Modules/Treasury/Accounts/Domain/TreasuryAccountRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Treasury\Accounts\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Treasury Account
 */
interface TreasuryAccountRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب حساب خزينة/بنك بناءً على رقم حساب الأستاذ العام المرتبط به.
     *
     * @param int $glAccountId
     * @param int $companyId
     * @return TreasuryAccount|null
     */
    public function findByGlAccount(int $glAccountId, int $companyId): ?TreasuryAccount;
}