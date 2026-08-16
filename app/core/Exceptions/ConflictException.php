<?php
// Path: app/Core/Exceptions/ConflictException.php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Throwable;

/**
 * Enterprise Conflict Exception
 * Thrown when a request conflicts with the current state of the server (e.g., duplicate unique keys).
 * Always returns a 409 status code.
 */
class ConflictException extends CoreException
{
    /**
     * ConflictException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $context
     */
    public function __construct(
        string $message = "The request could not be completed due to a conflict with the current state of the resource.",
        int $code = 409,
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous, $context);
    }
}