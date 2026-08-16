<?php
// Path: app/Core/Queue/RetryPolicy.php

declare(strict_types=1);

namespace App\Core\Queue;

/**
 * Enterprise Retry Policy
 * يحدد سياسة إعادة المحاولة للمهام الفاشلة (عدد المحاولات، ووقت الانتظار بين كل محاولة).
 */
class RetryPolicy
{
    public readonly int $maxAttempts;
    public readonly int $backoffSeconds;

    /**
     * RetryPolicy constructor.
     *
     * @param int $maxAttempts أقصى عدد للمحاولات قبل إعلان الفشل النهائي (الافتراضي 3)
     * @param int $backoffSeconds وقت الانتظار بالثواني قبل المحاولة التالية (الافتراضي 60)
     */
    public function __construct(int $maxAttempts = 3, int $backoffSeconds = 60)
    {
        $this->maxAttempts = max(1, $maxAttempts);
        $this->backoffSeconds = max(0, $backoffSeconds);
    }
} 