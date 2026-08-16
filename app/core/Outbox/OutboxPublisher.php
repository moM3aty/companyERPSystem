<?php
// Path: app/Core/Outbox/OutboxPublisher.php

declare(strict_types=1);

namespace App\Core\Outbox;

use App\Core\Events\Event;

/**
 * Enterprise Outbox Publisher
 * واجهة بديلة للـ EventBus. بدلاً من نشر الحدث فوراً (مما قد يسبب مشاكل إذا فشلت الداتابيز)،
 * يتم نشره عبر هذه الواجهة ليتم تخزينه بأمان داخل نفس الـ Transaction.
 */
class OutboxPublisher
{
    protected OutboxRepository $repository;

    /**
     * OutboxPublisher constructor.
     *
     * @param OutboxRepository $repository
     */
    public function __construct(OutboxRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * حفظ الحدث في صندوق الصادر (Outbox) ليتم توزيعه لاحقاً.
     *
     * @param Event $event
     * @return void
     */
    public function publish(Event $event): void
    {
        $this->repository->saveMessage(
            $event->getName(),
            $event->toPayload()
        );
    }
}