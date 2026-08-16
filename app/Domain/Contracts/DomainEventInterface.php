<?php
// Path: app/Domain/Contracts/DomainEventInterface.php

declare(strict_types=1);

namespace App\Domain\Contracts;

use DateTimeImmutable;

/**
 * Enterprise Domain Contract: Domain Event
 * الواجهة التي يجب أن يلتزم بها أي حدث ينطلق من طبقة ה-Domain.
 */
interface DomainEventInterface
{
    public function getEventId(): string;
    
    public function getOccurredOn(): DateTimeImmutable;
    
    public function getName(): string;
    
    public function toPayload(): array;
}