<?php
// Path: app/Core/Exceptions/IntegrationException.php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Throwable;

/**
 * Enterprise Integration Exception
 * Thrown when a third-party API or external service fails to respond or returns an error.
 * Returns a 502 Bad Gateway by default.
 */
class IntegrationException extends CoreException
{
    /**
     * IntegrationException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $context
     */
    public function __construct(
        string $message = "An error occurred while communicating with an external service.",
        int $code = 502,
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous, $context);
    }
}