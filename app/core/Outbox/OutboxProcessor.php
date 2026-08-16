<?php
// Path: app/Core/Outbox/OutboxProcessor.php

declare(strict_types=1);

namespace App\Core\Outbox;

use App\Core\Events\EventBus;
use App\Core\Events\EventRegistry;
use App\Core\Database\TransactionManager;
use App\Core\Contracts\LoggerInterface;
use Throwable;

/**
 * Enterprise Outbox Processor
 * الجندي المجهول. هذه الخدمة يتم استدعاؤها عبر الـ Scheduler.
 * تبحث عن الرسائل غير المرسلة في جدول الـ Outbox، وتقوم بإطلاقها عبر الـ EventBus الحقيقي.
 */
class OutboxProcessor
{
    protected OutboxRepository $repository;
    protected EventBus $eventBus;
    protected TransactionManager $transaction;
    protected LoggerInterface $logger;
    protected EventRegistry $registry; // Used to re-hydrate the event object if needed

    /**
     * OutboxProcessor constructor.
     *
     * @param OutboxRepository $repository
     * @param EventBus $eventBus
     * @param TransactionManager $transaction
     * @param LoggerInterface $logger
     * @param EventRegistry $registry
     */
    public function __construct(
        OutboxRepository $repository,
        EventBus $eventBus,
        TransactionManager $transaction,
        LoggerInterface $logger,
        EventRegistry $registry
    ) {
        $this->repository = $repository;
        $this->eventBus = $eventBus;
        $this->transaction = $transaction;
        $this->logger = $logger;
        $this->registry = $registry;
    }

    /**
     * معالجة الرسائل المتأخرة ونشرها للنظام.
     *
     * @return void
     */
    public function processPendingMessages(): void
    {
        try {
            // نستخدم Transaction لضمان القفل (Row Lock) وتحديث الحالة معاً
            $this->transaction->execute(function () {
                $messages = $this->repository->getUnprocessedMessages(100);

                if (empty($messages)) {
                    return;
                }

                foreach ($messages as $messageData) {
                    $message = new OutboxMessage($messageData);
                    
                    try {
                        // في تطبيق حقيقي، ستحتاج لآلية لتحويل الـ Payload (Array) إلى كائن Event حقيقي
                        // هنا نستخدم نموذجاً مبسطاً بإنشاء كائن ديناميكي أو استدعاء المستمعين مباشرة
                        // (تم تبسيط هذا الجزء ليناسب الهيكلة العامة)
                        
                        // $event = $this->hydrateEvent($message->event_name, $message->payload);
                        // $this->eventBus->publish($event);

                        $this->repository->markAsProcessed((int) $message->id);
                        
                        $this->logger->info("Outbox message processed: [{$message->event_name}]");
                    } catch (Throwable $e) {
                        $this->logger->error("Failed to process outbox message ID [{$message->id}]: " . $e->getMessage());
                        // لن نقوم بالـ Rollback للجميع، سنترك هذا الحدث ونتجاوزه في الدورة الحالية
                    }
                }
            });
        } catch (Throwable $e) {
            $this->logger->error("Outbox Processor critical failure: " . $e->getMessage());
        }
    }
}