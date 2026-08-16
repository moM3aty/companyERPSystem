<?php
// Path: app/Core/Events/Event.php

declare(strict_types=1);

namespace App\Core\Events;

use App\Core\Helpers\Str;

/**
 * Enterprise Base Event
 * الفئة الأساسية لكل الأحداث في النظام. تضمن وجود معرف فريد ووقت زمني دقيق لكل حدث.
 */
abstract class Event
{
    public readonly string $eventId;
    public readonly string $occurredOn;

    /**
     * Event constructor.
     */
    public function __construct()
    {
        $this->eventId = Str::uuid();
        $this->occurredOn = date('Y-m-d H:i:s.u'); // Includes microseconds for precise ordering
    }

    /**
     * جلب اسم الحدث بناءً على اسم الكلاس.
     *
     * @return string
     */
    public function getName(): string
    {
        return static::class;
    }

    /**
     * استخراج بيانات الحدث لتحويله إلى مصفوفة (مفيد للتخزين أو الإرسال عبر الـ Queue).
     *
     * @return array
     */
    abstract public function toPayload(): array;
}