<?php
// Path: app/Core/Localization/DateFormatter.php

declare(strict_types=1);

namespace App\Core\Localization;

use DateTime;
use Exception;

/**
 * Enterprise Date Formatter
 * يقوم بتنسيق التاريخ وعرضه بشكل مناسب محلياً بعد تحويله للتوقيت الصحيح.
 */
class DateFormatter
{
    protected TimezoneManager $timezoneManager;
    
    // الصيغ الافتراضية
    protected string $defaultDateFormat = 'Y-m-d';
    protected string $defaultDateTimeFormat = 'Y-m-d h:i A';

    /**
     * DateFormatter constructor.
     *
     * @param TimezoneManager $timezoneManager
     */
    public function __construct(TimezoneManager $timezoneManager)
    {
        $this->timezoneManager = $timezoneManager;
    }

    /**
     * فرمتة تاريخ ووقت قادم من قاعدة البيانات (UTC) ليعرض للمستخدم بصيغة مقروءة.
     *
     * @param string $utcDatetime
     * @param string|null $format
     * @return string
     */
    public function formatDateTime(string $utcDatetime, ?string $format = null): string
    {
        try {
            $localDatetime = $this->timezoneManager->convertFromUtc($utcDatetime);
            $date = new DateTime($localDatetime);
            return $date->format($format ?: $this->defaultDateTimeFormat);
        } catch (Exception $e) {
            // في حالة كان التاريخ غير صالح، يتم إرجاع النص كما هو كإجراء أمان
            return $utcDatetime;
        }
    }

    /**
     * فرمتة تاريخ فقط (بدون وقت).
     *
     * @param string $dateString
     * @param string|null $format
     * @return string
     */
    public function formatDate(string $dateString, ?string $format = null): string
    {
        try {
            $date = new DateTime($dateString);
            return $date->format($format ?: $this->defaultDateFormat);
        } catch (Exception $e) {
            return $dateString;
        }
    }
}