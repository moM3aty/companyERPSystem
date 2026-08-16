<?php
// Path: app/Core/Events/EventDispatcher.php

declare(strict_types=1);

namespace App\Core\Events;

use App\Core\Bootstrap\Container;
use Exception;
use App\Core\Monitoring\Logger;

/**
 * Enterprise Event Dispatcher
 * المحرك الرئيسي الذي يستقبل الحدث ويقوم بتوزيعه (Dispatch) على جميع المستمعين المسجلين.
 */
class EventDispatcher
{
    protected EventRegistry $registry;
    protected Container $container;
    protected Logger $logger;

    /**
     * EventDispatcher constructor.
     *
     * @param EventRegistry $registry
     * @param Container $container
     * @param Logger $logger
     */
    public function __construct(EventRegistry $registry, Container $container, Logger $logger)
    {
        $this->registry = $registry;
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * نشر حدث وتمريره لجميع المستمعين.
     *
     * @param Event $event
     * @return void
     */
    public function dispatch(Event $event): void
    {
        $eventName = $event->getName();
        $listeners = $this->registry->getListeners($eventName);

        if (empty($listeners)) {
            return;
        }

        foreach ($listeners as $listenerClass) {
            try {
                /** @var EventListener $listenerInstance */
                $listenerInstance = $this->container->make($listenerClass);
                
                // توجيه الحدث إلى المستمع
                $listenerInstance->handle($event);
                
            } catch (Exception $e) {
                // التقاط الخطأ حتى لا يتسبب فشل مستمع واحد (مثل إرسال إيميل) في إيقاف باقي العمليات
                $this->logger->error("Failed to execute listener [{$listenerClass}] for event [{$eventName}].", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }

    /**
     * تسجيل مستمع لحدث بشكل مباشر.
     *
     * @param string $eventName
     * @param string $listenerClass
     * @return void
     */
    public function listen(string $eventName, string $listenerClass): void
    {
        $this->registry->addListener($eventName, $listenerClass);
    }
}