<?php
// Path: app/Security/SecurityPolicies/Domain/SecurityPolicyRepositoryInterface.php

declare(strict_types=1);

namespace App\Security\SecurityPolicies\Domain;

interface SecurityPolicyRepositoryInterface
{
    /**
     * جلب السياسة الأمنية لشركة معينة.
     */
    public function getPolicyForCompany(int $companyId): ?SecurityPolicy;
}