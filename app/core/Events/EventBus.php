<?php
// Path: app/Core/Events/EventBus.php

declare(strict_types=1);

namespace App\Core\Events;

/**
 * Enterprise Event Bus
 * واجهة عالية المستوى (Facade) لتسهيل إطلاق الأحداث من داخل الـ Controllers و الـ Repositories.
 */
class EventBus
{
    protected EventDispatcher $dispatcher;

    /**
     * EventBus constructor.
     *
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * نشر حدث جديد في النظام.
     *
     * @param Event $event
     * @return void
     */
    public function publish(Event $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    /**
     * نشر مجموعة من الأحداث دفعة واحدة.
     *
     * @param array<Event> $events
     * @return void
     */
    public function publishMultiple(array $events): void
    {
        foreach ($events as $event) {
            if ($event instanceof Event) {
                $this->dispatcher->dispatch($event);
            }
        }
    }
}