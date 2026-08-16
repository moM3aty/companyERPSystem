<?php
// Path: app/Core/Exceptions/NotFoundException.php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Throwable;

/**
 * Enterprise Not Found Exception
 * Thrown when a requested resource (database record, file, route) cannot be found.
 * Always returns a 404 status code.
 */
class NotFoundException extends CoreException
{
    /**
     * NotFoundException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $context
     */
    public function __construct(
        string $message = "The requested resource was not found.",
        int $code = 404,
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous, $context);
    }
}