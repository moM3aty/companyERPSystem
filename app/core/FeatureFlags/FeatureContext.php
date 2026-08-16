<?php
// Path: app/Core/FeatureFlags/FeatureContext.php

declare(strict_types=1);

namespace App\Core\FeatureFlags;

/**
 * Enterprise Feature Context
 * يغلف البيانات اللحظية (المستخدم والشركة) لفحص ما إذا كانت الميزة مفعلة لهم.
 */
class FeatureContext
{
    public readonly ?int $userId;
    public readonly ?int $companyId;
    public readonly ?int $branchId;

    /**
     * FeatureContext constructor.
     *
     * @param int|null $userId
     * @param int|null $companyId
     * @param int|null $branchId
     */
    public function __construct(?int $userId = null, ?int $companyId = null, ?int $branchId = null)
    {
        $this->userId = $userId;
        $this->companyId = $companyId;
        $this->branchId = $branchId;
    }
}