<?php
// Path: app/Domain/Entities/AggregateRoot.php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Contracts\AggregateRootInterface;
use App\Domain\Contracts\DomainEventInterface;

/**
 * Enterprise Domain Base: Aggregate Root
 * فئة الأساس للكيانات الجذرية (مثل: أمر البيع، مسير الرواتب) التي تصدر أحداثاً (Domain Events).
 */
abstract class AggregateRoot extends Entity implements AggregateRootInterface
{
    /**
     * @var array<DomainEventInterface>
     */
    protected array $domainEvents = [];

    /**
     * تسجيل حدث (Event) داخلياً تمهيداً لإطلاقه لاحقاً عند الحفظ (Commit).
     *
     * @param DomainEventInterface $event
     * @return void
     */
    protected function record(DomainEventInterface $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * @inheritDoc
     */
    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    /**
     * @inheritDoc
     */
    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }
}