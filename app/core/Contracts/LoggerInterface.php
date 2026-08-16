<?php
// Path: app/Core/Contracts/LoggerInterface.php

declare(strict_types=1);

namespace App\Core\Contracts;

/**
 * Enterprise Logger Interface
 * متوافق تماماً مع معيار PSR-3 العالمي لتسجيل الأحداث والأخطاء.
 * يضمن إمكانية تغيير محرك الـ Logging مستقبلاً (مثلاً إلى DataDog أو AWS CloudWatch) دون المساس بالكود.
 */
interface LoggerInterface
{
    /**
     * النظام غير قابل للاستخدام (System is unusable).
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency(string $message, array $context = []): void;

    /**
     * يجب اتخاذ إجراء فوري (Action must be taken immediately).
     * مثال: تعطل قاعدة البيانات بالكامل.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert(string $message, array $context = []): void;

    /**
     * حالات حرجة (Critical conditions).
     * مثال: استثناء غير متوقع في مكون رئيسي.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical(string $message, array $context = []): void;

    /**
     * أخطاء وقت التشغيل التي لا تتطلب تدخلاً فورياً ولكن يجب تسجيلها ومراقبتها.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(string $message, array $context = []): void;

    /**
     * أحداث استثنائية ليست أخطاء (Exceptional occurrences that are not errors).
     * مثال: استخدام واجهة برمجة تطبيقات قديمة (Deprecated API).
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(string $message, array $context = []): void;

    /**
     * أحداث طبيعية لكنها ذات أهمية (Normal but significant events).
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice(string $message, array $context = []): void;

    /**
     * أحداث عامة مثيرة للاهتمام (Interesting events).
     * مثال: تسجيل دخول مستخدم، إنشاء فاتورة.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info(string $message, array $context = []): void;

    /**
     * معلومات تفصيلية لأغراض التصحيح (Detailed debug information).
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug(string $message, array $context = []): void;

    /**
     * تسجيل رسالة بمستوى مخصص.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log(mixed $level, string $message, array $context = []): void;
}