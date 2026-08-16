<?php
// Path: app/Compliance/DataRetention/Domain/RetentionPolicyRepositoryInterface.php

declare(strict_types=1);

namespace App\Compliance\DataRetention\Domain;

use App\Core\Contracts\RepositoryInterface;

interface RetentionPolicyRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب جميع السياسات النشطة.
     *
     * @return array
     */
    public function getActivePolicies(): array;
}