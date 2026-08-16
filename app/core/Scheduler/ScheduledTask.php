<?php
// Path: app/Core/Scheduler/ScheduledTask.php

declare(strict_types=1);

namespace App\Core\Scheduler;

use App\Core\Bootstrap\Container;
use Closure;

/**
 * Enterprise Scheduled Task
 * يمثل مهمة واحدة تمت جدولتها (اسم المهمة، موعد التنفيذ، وماذا ستفعل).
 */
class ScheduledTask
{
    protected string $expression = '* * * * *';
    protected mixed $callback;
    protected string $description;
    protected bool $withoutOverlapping = false;

    /**
     * ScheduledTask constructor.
     *
     * @param mixed $callback (Closure, Job Class Name, or Command)
     */
    public function __construct(mixed $callback)
    {
        $this->callback = $callback;
    }

    /**
     * تعيين وصف للمهمة للـ Logging.
     *
     * @param string $description
     * @return self
     */
    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * تعيين تعبير الـ Cron مباشرة.
     *
     * @param string $expression
     * @return self
     */
    public function cron(string $expression): self
    {
        $this->expression = $expression;
        return $this;
    }

    /**
     * الجدولة لتنفيذ المهمة كل دقيقة.
     *
     * @return self
     */
    public function everyMinute(): self
    {
        return $this->cron('* * * * *');
    }

    /**
     * الجدولة لتنفيذ المهمة كل 5 دقائق.
     *
     * @return self
     */
    public function everyFiveMinutes(): self
    {
        return $this->cron('*/5 * * * *');
    }

    /**
     * الجدولة لتنفيذ المهمة كل ساعة.
     *
     * @return self
     */
    public function hourly(): self
    {
        return $this->cron('0 * * * *');
    }

    /**
     * الجدولة لتنفيذ المهمة يومياً عند منتصف الليل.
     *
     * @return self
     */
    public function daily(): self
    {
        return $this->cron('0 0 * * *');
    }

    /**
     * منع تداخل المهام (لا تقم بتشغيل المهمة إذا كانت النسخة السابقة منها ما زالت تعمل).
     *
     * @return self
     */
    public function withoutOverlapping(): self
    {
        $this->withoutOverlapping = true;
        return $this;
    }

    /**
     * الحصول على تعبير الـ Cron.
     *
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * الحصول على الوصف.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description ?? 'Unnamed Task';
    }

    /**
     * تنفيذ المهمة فعلياً.
     *
     * @param Container $container
     * @return void
     */
    public function run(Container $container): void
    {
        if ($this->callback instanceof Closure) {
            $container->call($this->callback);
        } elseif (is_string($this->callback) && class_exists($this->callback)) {
            // إذا كانت المهمة عبارة عن كلاس (مثلاً Job)، نقوم بإنشائه وتشغيله
            $instance = $container->make($this->callback);
            if (method_exists($instance, 'handle')) {
                $container->call([$instance, 'handle']);
            }
        }
    }
}