<?php
// Path: app/Core/Exceptions/AuthorizationException.php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Throwable;

/**
 * Enterprise Authorization Exception
 * يتم رمي هذا الاستثناء عندما يحاول المستخدم تنفيذ إجراء ليس لديه الصلاحية له (403 Forbidden).
 */
class AuthorizationException extends CoreException
{
    /**
     * AuthorizationException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $context
     */
    public function __construct(
        string $message = 'Access Denied: You do not have the required permissions to perform this action.',
        int $code = 403,
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous, $context);
    }
}