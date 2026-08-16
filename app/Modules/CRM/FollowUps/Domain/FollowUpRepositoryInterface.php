<?php
// Path: app/Modules/CRM/FollowUps/Domain/FollowUpRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\CRM\FollowUps\Domain;

use App\Core\Contracts\RepositoryInterface;

interface FollowUpRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب المتابعات المستحقة لمندوب مبيعات معين.
     *
     * @param int $userId
     * @param int $companyId
     * @param string $date
     * @return array
     */
    public function getPendingForUser(int $userId, int $companyId, string $date): array;
}