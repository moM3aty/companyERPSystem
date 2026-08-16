<?php
// Path: app/Core/Events/EventRegistry.php

declare(strict_types=1);

namespace App\Core\Events;

/**
 * Enterprise Event Registry
 * خريطة (Map) تحفظ أسماء الأحداث وتربطها بأسماء كلاسات المستمعين (Listeners).
 */
class EventRegistry
{
    /**
     * مصفوفة ربط الأحداث بالمستمعين.
     * [EventClass => [ListenerClass1, ListenerClass2]]
     *
     * @var array
     */
    protected array $listeners = [];

    /**
     * إضافة مستمع لحدث معين.
     *
     * @param string $eventName
     * @param string $listenerClass
     * @return void
     */
    public function addListener(string $eventName, string $listenerClass): void
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }

        if (!in_array($listenerClass, $this->listeners[$eventName], true)) {
            $this->listeners[$eventName][] = $listenerClass;
        }
    }

    /**
     * جلب جميع المستمعين لحدث معين.
     *
     * @param string $eventName
     * @return array
     */
    public function getListeners(string $eventName): array
    {
        return $this->listeners[$eventName] ?? [];
    }

    /**
     * التحقق مما إذا كان الحدث يمتلك مستمعين.
     *
     * @param string $eventName
     * @return bool
     */
    public function hasListeners(string $eventName): bool
    {
        return !empty($this->listeners[$eventName]);
    }
}