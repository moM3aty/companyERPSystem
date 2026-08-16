<?php
// Path: app/Core/Contracts/IntegrationInterface.php

declare(strict_types=1);

namespace App\Core\Contracts;

/**
 * Enterprise Integration Interface
 * Standardizes API communication with external 3rd-party services (e.g., ZATCA, Payment Gateways).
 */
interface IntegrationInterface
{
    /**
     * Authenticate and initialize the connection with the external service.
     *
     * @return bool
     * @throws \App\Core\Exceptions\IntegrationException
     */
    public function connect(): bool;

    /**
     * Send a payload to the external service.
     *
     * @param string $endpoint
     * @param array $payload
     * @param string $method GET, POST, etc.
     * @return array
     * @throws \App\Core\Exceptions\IntegrationException
     */
    public function send(string $endpoint, array $payload = [], string $method = 'POST'): array;
}