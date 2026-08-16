<?php
// Path: app/Core/Settings/BranchSettings.php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Branch Settings
 * واجهة خاصة بإعدادات الفرع (مثل إيصالات الكاشير للفرع، الطابعات، مستودع الفرع الافتراضي).
 */
class BranchSettings extends TenantSettings
{

    /**
     * @inheritDoc
     */
    protected function getScope(): string
    {
        return Setting::SCOPE_BRANCH;
    }

    /**
     * @inheritDoc
     */
    protected function getScopeId(): int
    {
        $branchId = $this->tenantContext->requireTenant()->branchId;

        if ($branchId === null) {
            throw new BusinessException("Branch context is required to access Branch Settings.", 403);
        }

        return $branchId;
    }
}