<?php
// Path: app/Modules/POS/Shifts/Domain/PosShiftRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\POS\Shifts\Domain;

use App\Core\Contracts\RepositoryInterface;

interface PosShiftRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب الوردية المفتوحة حالياً لمستخدم (كاشير) معين.
     *
     * @param int $userId
     * @param int $companyId
     * @return array|null
     */
    public function getActiveShiftForUser(int $userId, int $companyId): ?array;
}