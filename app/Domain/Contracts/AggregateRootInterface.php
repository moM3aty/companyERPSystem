<?php
// Path: app/Domain/Contracts/AggregateRootInterface.php

declare(strict_types=1);

namespace App\Domain\Contracts;

/**
 * Enterprise Domain Contract: Aggregate Root Interface
 * يمثل الكيان الأساسي (الجذر) في مجموعة الكيانات المرتبطة.
 * هو الوحيد المسموح له بتسجيل الأحداث وإدارتها داخل نطاقه (Transaction Boundary).
 */
interface AggregateRootInterface
{
    /**
     * جلب كافة الأحداث (Domain Events) التي سجلها هذا الكيان.
     *
     * @return array<DomainEventInterface>
     */
    public function getDomainEvents(): array;

    /**
     * مسح الأحداث المسجلة بعد ترحيلها بنجاح للـ EventBus.
     *
     * @return void
     */
    public function clearDomainEvents(): void;
}