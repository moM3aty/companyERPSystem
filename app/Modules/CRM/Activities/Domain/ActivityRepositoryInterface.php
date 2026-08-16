<?php
// Path: app/Modules/CRM/Activities/Domain/ActivityRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\CRM\Activities\Domain;

use App\Core\Contracts\RepositoryInterface;

interface ActivityRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب الأنشطة المعلقة (التي تحتاج متابعة) لمندوب مبيعات معين.
     *
     * @param int $userId
     * @param int $companyId
     * @return array
     */
    public function getPendingActivitiesForUser(int $userId, int $companyId): array;
}