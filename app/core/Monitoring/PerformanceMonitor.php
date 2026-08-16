<?php
// Path: app/Core/Monitoring/PerformanceMonitor.php

declare(strict_types=1);

namespace App\Core\Monitoring;

/**
 * Enterprise Performance Monitor
 * يقيس استهلاك الذاكرة ووقت تنفيذ الطلبات ويسجل تحذير إذا تجاوزت الحدود الآمنة.
 */
class PerformanceMonitor
{
    protected Logger $logger;
    
    /**
     * أقصى وقت مقبول لتنفيذ الطلب بالمللي ثانية (مثال: 2 ثانية).
     */
    protected float $executionThresholdMs = 2000.0;
    
    /**
     * أقصى استهلاك للذاكرة بالميجابايت (مثال: 128 ميجا).
     */
    protected float $memoryThresholdMb = 128.0;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * يجب استدعاؤها في نهاية دورة حياة الطلب (في Kernel->terminate).
     *
     * @param string $route المسار الذي تم طلبه
     * @return void
     */
    public function record(string $route): void
    {
        if (!defined('ERP_START_TIME')) {
            return;
        }

        $executionTimeMs = (microtime(true) - ERP_START_TIME) * 1000;
        $peakMemoryMb = memory_get_peak_usage(true) / 1024 / 1024;

        if ($executionTimeMs > $this->executionThresholdMs) {
            $this->logger->warning("Performance Alert: Route [{$route}] is too slow.", [
                'execution_time_ms' => round($executionTimeMs, 2),
                'threshold_ms' => $this->executionThresholdMs
            ]);
        }

        if ($peakMemoryMb > $this->memoryThresholdMb) {
            $this->logger->warning("Performance Alert: Route [{$route}] is consuming too much memory.", [
                'peak_memory_mb' => round($peakMemoryMb, 2),
                'threshold_mb' => $this->memoryThresholdMb
            ]);
        }
    }
}