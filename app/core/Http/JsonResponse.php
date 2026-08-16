<?php
// Path: app/Core/Http/JsonResponse.php

declare(strict_types=1);

namespace App\Core\Http;

use InvalidArgumentException;

/**
 * Enterprise JSON HTTP Response
 * Specifically handles API responses by automatically encoding arrays/objects to JSON securely.
 */
class JsonResponse extends Response
{
    /**
     * Create a new JSON Response instance.
     *
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct(mixed $data = null, int $statusCode = 200, array $headers = [])
    {
        parent::__construct('', $statusCode, $headers);
        
        $this->setHeader('Content-Type', 'application/json; charset=utf-8');
        $this->setData($data);
    }

    /**
     * Set the data to be formatted as JSON.
     *
     * @param mixed $data
     * @return self
     * @throws InvalidArgumentException
     */
    public function setData(mixed $data): self
    {
        // Use strict flags for secure and uniform JSON outputs
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('Invalid JSON data provided: ' . json_last_error_msg());
        }

        $this->content = $json;

        return $this;
    }
}