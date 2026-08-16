<?php
// Path: app/Core/Events/EventSubscriber.php

declare(strict_types=1);

namespace App\Core\Events;

/**
 * Enterprise Event Subscriber Interface
 * يسمح بتجميع الاستماع لعدة أحداث داخل كلاس واحد (Subscriber) وتسجيلهم دفعة واحدة.
 */
interface EventSubscriber
{
    /**
     * تسجيل المستمعين للأحداث المطلوبة عبر الـ Dispatcher.
     *
     * @param EventDispatcher $dispatcher
     * @return void
     */
    public function subscribe(EventDispatcher $dispatcher): void;
}