<?php
// Path: app/Core/Exceptions/ConfigurationException.php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Throwable;

/**
 * Enterprise Configuration Exception
 * Thrown when there is a critical misconfiguration in the system (e.g., missing API keys, invalid paths).
 * Always returns a 500 status code as it prevents the system from running.
 */
class ConfigurationException extends CoreException
{
    /**
     * ConfigurationException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $context
     */
    public function __construct(
        string $message = "A critical system configuration error occurred.",
        int $code = 500,
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous, $context);
    }
}