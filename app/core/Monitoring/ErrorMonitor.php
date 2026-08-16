<?php
// Path: app/Core/Monitoring/ErrorMonitor.php

declare(strict_types=1);

namespace App\Core\Monitoring;

use Throwable;

/**
 * Enterprise Error Monitor
 * خدمة متخصصة في تتبع الأخطاء الحرجة، يمكن ربطها لاحقاً بخدمات خارجية مثل Sentry أو Bugsnag.
 */
class ErrorMonitor
{
    protected Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * تسجيل استثناء غير معالج وتصنيفه.
     *
     * @param Throwable $e
     * @param array $additionalContext
     * @return void
     */
    public function captureException(Throwable $e, array $additionalContext = []): void
    {
        $context = array_merge([
            'exception_class' => get_class($e),
            'file'            => $e->getFile(),
            'line'            => $e->getLine(),
            'trace'           => $e->getTraceAsString(),
        ], $additionalContext);

        $this->logger->critical("Unhandled Exception: " . $e->getMessage(), $context);

        // هنا يمكن إضافة الكود الخاص بإرسال الخطأ إلى Sentry
        // if (class_exists('\Sentry\SentrySdk')) { ... }
    }

    /**
     * تسجيل خطأ منطقي أو تحذير تنفيذي لا يرقى لأن يكون Exception يوقف النظام.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function captureMessage(string $message, array $context = []): void
    {
        $this->logger->error("System Monitor Error: " . $message, $context);
    }
}