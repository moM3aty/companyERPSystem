<?php
// Path: app/Core/Localization/TimezoneManager.php

declare(strict_types=1);

namespace App\Core\Localization;

use DateTime;
use DateTimeZone;
use Exception;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Timezone Manager
 * يحل المعضلة الكبرى في الـ ERP: حفظ جميع الأوقات كـ UTC في الداتابيز،
 * ثم تحويلها تلقائياً إلى توقيت العميل أو الفرع أو الشركة عند العرض.
 */
class TimezoneManager
{
    protected TenantContext $tenantContext;
    protected string $systemTimezone = 'UTC';

    /**
     * TimezoneManager constructor.
     *
     * @param TenantContext $tenantContext
     */
    public function __construct(TenantContext $tenantContext)
    {
        $this->tenantContext = $tenantContext;
    }

    /**
     * جلب توقيت المستخدم أو الشركة الحالية.
     *
     * @return string
     */
    public function getCurrentTimezone(): string
    {
        $tenant = $this->tenantContext->getTenant();
        
        return $tenant ? $tenant->timezone : date_default_timezone_get();
    }

    /**
     * تحويل وقت من توقيت المستخدم إلى توقيت النظام الأساسي (UTC) قبل الحفظ في قاعدة البيانات.
     *
     * @param string $datetime
     * @param string|null $fromTimezone
     * @return string (صيغة Y-m-d H:i:s)
     * @throws Exception
     */
    public function convertToUtc(string $datetime, ?string $fromTimezone = null): string
    {
        $sourceTz = $fromTimezone ?: $this->getCurrentTimezone();
        
        $date = new DateTime($datetime, new DateTimeZone($sourceTz));
        $date->setTimezone(new DateTimeZone($this->systemTimezone));
        
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * تحويل وقت من قاعدة البيانات (UTC) إلى توقيت المستخدم الحالي للعرض في الواجهة.
     *
     * @param string $datetime
     * @param string|null $toTimezone
     * @return string (صيغة Y-m-d H:i:s)
     * @throws Exception
     */
    public function convertFromUtc(string $datetime, ?string $toTimezone = null): string
    {
        $targetTz = $toTimezone ?: $this->getCurrentTimezone();
        
        $date = new DateTime($datetime, new DateTimeZone($this->systemTimezone));
        $date->setTimezone(new DateTimeZone($targetTz));
        
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * جلب الوقت الحالي بتوقيت السيرفر العالمي (UTC) لتسجيل الـ Logs والأحداث.
     *
     * @return string
     */
    public function nowUtc(): string
    {
        return gmdate('Y-m-d H:i:s');
    }
}