<?php
// Path: app/Core/Exceptions/AuthenticationException.php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Throwable;

/**
 * Enterprise Authentication Exception
 * يُرمى عند فشل تسجيل الدخول أو إذا كانت الجلسة / التوكن غير صالح.
 * يُرجع كود 401.
 */
class AuthenticationException extends CoreException
{
    /**
     * AuthenticationException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $context
     */
    public function __construct(
        string $message = "Unauthenticated.",
        int $code = 401,
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous, $context);
    }
}