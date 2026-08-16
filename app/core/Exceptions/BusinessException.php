<?php
// Path: app/Core/Exceptions/BusinessException.php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Throwable;

/**
 * Enterprise Business Exception
 * يُرمى عندما يكون هناك انتهاك لقواعد العمل (مثال: رصيد غير كافي، قيد غير متزن).
 * يُرجع دائماً كود 422 (Unprocessable Entity).
 */
class BusinessException extends CoreException
{
    /**
     * BusinessException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $context
     */
    public function __construct(
        string $message = "A business rule violation occurred.",
        int $code = 422,
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous, $context);
    }
}