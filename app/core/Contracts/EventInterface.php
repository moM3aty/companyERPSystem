<?php
// Path: app/Core/Contracts/EventInterface.php

declare(strict_types=1);

namespace App\Core\Contracts;

/**
 * Enterprise Event Interface
 * Represents a domain event that occurs within the ERP (e.g., InvoiceCreated).
 */
interface EventInterface
{
    /**
     * Get the name of the event.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the payload/data associated with the event.
     *
     * @return array
     */
    public function getPayload(): array;
}