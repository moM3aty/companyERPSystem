<?php
// Path: app/Core/Scheduler/CronExpression.php

declare(strict_types=1);

namespace App\Core\Scheduler;

use DateTimeInterface;

/**
 * Enterprise Cron Expression Parser
 * يقوم بتحليل تعبيرات الـ Cron (مثل * * * * *) لتحديد ما إذا كان وقت تنفيذ المهمة قد حان.
 * يدعم القيم المباشرة، النطاقات (1-5)، الفواصل (1,2,3)، والخطوات (*/5).
 */
class CronExpression
{
    protected string $expression;

    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }

    /**
     * التحقق مما إذا كان تعبير الـ Cron ينطبق على الوقت المعطى.
     *
     * @param DateTimeInterface $date
     * @return bool
     */
    public function isDue(DateTimeInterface $date): bool
    {
        $parts = explode(' ', $this->expression);
        
        if (count($parts) !== 5) {
            throw new \InvalidArgumentException("Invalid cron expression: {$this->expression}. Must have 5 parts.");
        }

        list($minute, $hour, $day, $month, $dayOfWeek) = $parts;

        return $this->matchPart($minute, (int) $date->format('i')) &&
               $this->matchPart($hour, (int) $date->format('H')) &&
               $this->matchPart($day, (int) $date->format('d')) &&
               $this->matchPart($month, (int) $date->format('m')) &&
               $this->matchPart($dayOfWeek, (int) $date->format('w'));
    }

    /**
     * مطابقة جزء محدد من التعبير مع القيمة الحالية.
     *
     * @param string $part (e.g., '*', '*/5', '1-5', '1,2,3', '10')
     * @param int $currentValue
     * @return bool
     */
    protected function matchPart(string $part, int $currentValue): bool
    {
        if ($part === '*') {
            return true;
        }

        // دعم الخطوات (مثال: */5 كل 5 دقائق)
        if (str_contains($part, '/')) {
            list($range, $step) = explode('/', $part, 2);
            $step = (int) $step;
            
            if ($range === '*') {
                return $currentValue % $step === 0;
            }
            
            // دعم النطاق مع الخطوة (مثال: 1-10/2)
            if (str_contains($range, '-')) {
                list($min, $max) = explode('-', $range, 2);
                return $currentValue >= (int)$min && $currentValue <= (int)$max && $currentValue % $step === 0;
            }
        }

        // دعم القوائم (مثال: 1,15,30)
        if (str_contains($part, ',')) {
            $list = array_map('intval', explode(',', $part));
            return in_array($currentValue, $list, true);
        }

        // دعم النطاقات (مثال: 9-17)
        if (str_contains($part, '-')) {
            list($min, $max) = explode('-', $part, 2);
            return $currentValue >= (int)$min && $currentValue <= (int)$max;
        }

        // قيمة مباشرة (مثال: 15)
        return $currentValue === (int) $part;
    }
}