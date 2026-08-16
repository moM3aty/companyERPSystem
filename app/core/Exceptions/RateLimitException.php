<?php
// Path: app/Core/Exceptions/RateLimitException.php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Throwable;

/**
 * Enterprise Rate Limit Exception
 * يُرمى عند تجاوز المستخدم عدد الطلبات المسموحة (Brute Force / DDoS).
 */
class RateLimitException extends CoreException
{
    public readonly int $retryAfter;

    /**
     * RateLimitException constructor.
     *
     * @param string $message
     * @param int $retryAfter الوقت المتبقي لفك الحظر بالثواني
     * @param int $code
     * @param Throwable|null $previous
     * @param array $context
     */
    public function __construct(
        string $message = "Too many requests. Please slow down.",
        int $retryAfter = 60,
        int $code = 429,
        ?Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous, $context);
        $this->retryAfter = $retryAfter;
        // هنا تم إصلاح خطأ استخدام $this->context
        $this->context = array_merge($this->context, ['retry_after' => $retryAfter]);
    }
}