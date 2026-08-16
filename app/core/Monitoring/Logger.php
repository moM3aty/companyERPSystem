<?php
// Path: app/Core/Monitoring/Logger.php

declare(strict_types=1);

namespace App\Core\Monitoring;

use App\Core\Config\Config;
use App\Core\Contracts\LoggerInterface;
use RuntimeException;

/**
 * Enterprise File Logger
 * يقوم بتسجيل الأحداث والأخطاء في ملفات نصية مع دعم التقسيم اليومي (Daily Rotation).
 */
class Logger implements LoggerInterface
{
    protected string $logPath;
    protected ?LogContext $defaultContext = null;

    /**
     * Logger constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $appRoot = rtrim($config->get('app.root'), '\/');
        $this->logPath = $appRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';

        // إنشاء مجلد الـ Logs إذا لم يكن موجوداً
        if (!is_dir($this->logPath)) {
            if (!mkdir($this->logPath, 0755, true)) {
                throw new RuntimeException("Failed to create log directory at: {$this->logPath}");
            }
        }
    }

    /**
     * تعيين سياق افتراضي ليتم إرفاقه مع جميع السجلات اللاحقة.
     *
     * @param LogContext $context
     * @return self
     */
    public function setDefaultContext(LogContext $context): self
    {
        $this->defaultContext = $context;
        return $this;
    }


    public function emergency(string $message, array $context = []): void
    {
        $this->log('EMERGENCY', $message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->log('ALERT', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->log('NOTICE', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }


    /**
     * التنفيذ الفعلي لعملية التسجيل في الملف.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log(mixed $level, string $message, array $context = []): void
    {
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $levelStr = strtoupper((string) $level);

        // اسم الملف بناءً على التاريخ (Daily Rotation)
        $fileName = "erp-{$date}.log";
        $filePath = $this->logPath . DIRECTORY_SEPARATOR . $fileName;

        // دمج السياق الافتراضي (إن وجد) مع السياق المُمرر
        $mergedContext = $context;
        if ($this->defaultContext !== null) {
            $mergedContext = array_merge($this->defaultContext->toArray(), $context);
        }

        // تنسيق السياق كـ JSON لسهولة قراءته بواسطة أدوات تحليل الـ Logs مثل (ELK Stack)
        $contextJson = !empty($mergedContext) ? ' ' . json_encode($mergedContext, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';

        // صياغة السطر النهائي
        // مثال: [2026-08-14 17:45:00] [ERROR] Database connection failed. {"company_id": 5}
        $logEntry = sprintf("[%s %s] [%s] %s%s" . PHP_EOL, $date, $time, $levelStr, $message, $contextJson);

        // الكتابة في الملف مع قفل حصري (LOCK_EX) لمنع تداخل الكتابة إذا تم استدعاء الدالة من عدة مستخدمين في نفس اللحظة
        file_put_contents($filePath, $logEntry, FILE_APPEND | LOCK_EX);
    }
}