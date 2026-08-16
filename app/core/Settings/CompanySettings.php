<?php
// Path: app/Core/Settings/CompanySettings.php

declare(strict_types=1);

namespace App\Core\Settings;

/**
 * Enterprise Company Settings
 * واجهة خاصة بإعدادات الشركة الحالية (مثل الضرائب الافتراضية، توقيع الفاتورة، اللوجو).
 * مدمجة تماماً مع الـ TenantContext للحماية التلقائية.
 */
class CompanySettings extends TenantSettings
{

    /**
     * @inheritDoc
     */
    protected function getScope(): string
    {
        return Setting::SCOPE_COMPANY;
    }

    /**
     * @inheritDoc
     */
    protected function getScopeId(): int
    {
        // الدالة requireTenant ترمي Exception قوي في حال عدم وجود شركة مفعلة
        return $this->tenantContext->requireTenant()->companyId;
    }
}