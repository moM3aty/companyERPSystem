<?php
// Path: app/Core/Monitoring/Metrics.php

declare(strict_types=1);

namespace App\Core\Monitoring;

/**
 * Enterprise Metrics Collector
 * يجمع إحصائيات حول أداء النظام لاحقاً يمكن تصديرها لأنظمة مثل Prometheus أو Datadog.
 */
class Metrics
{
    /**
     * @var array
     */
    protected array $counters = [];

    /**
     * @var array
     */
    protected array $timers = [];

    /**
     * زيادة عداد معين (مثال: عدد محاولات الدخول الفاشلة).
     *
     * @param string $key
     * @param int $value
     * @return void
     */
    public function increment(string $key, int $value = 1): void
    {
        if (!isset($this->counters[$key])) {
            $this->counters[$key] = 0;
        }
        $this->counters[$key] += $value;
    }

    /**
     * بدء مؤقت لعملية معينة.
     *
     * @param string $key
     * @return void
     */
    public function startTimer(string $key): void
    {
        $this->timers[$key] = ['start' => microtime(true), 'end' => null];
    }

    /**
     * إيقاف المؤقت وتسجيل الوقت المستغرق.
     *
     * @param string $key
     * @return float|null الوقت بالمللي ثانية
     */
    public function stopTimer(string $key): ?float
    {
        if (!isset($this->timers[$key]) || !isset($this->timers[$key]['start'])) {
            return null;
        }

        $this->timers[$key]['end'] = microtime(true);
        $duration = ($this->timers[$key]['end'] - $this->timers[$key]['start']) * 1000;
        
        $this->timers[$key]['duration_ms'] = $duration;

        return $duration;
    }

    /**
     * جلب كافة الإحصائيات (لغرض العرض في الـ Dashboard أو التصدير).
     *
     * @return array
     */
    public function flush(): array
    {
        return [
            'counters' => $this->counters,
            'timers'   => $this->timers,
        ];
    }
}