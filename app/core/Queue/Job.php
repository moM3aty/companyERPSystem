<?php
// Path: app/Core/Queue/Job.php

declare(strict_types=1);

namespace App\Core\Queue;

use App\Core\Bootstrap\Container;
use Exception;

/**
 * Enterprise Base Job
 * الكلاس الأساسي الذي يجب أن ترث منه أي مهمة (Task) سيتم تنفيذها في الخلفية.
 */
abstract class Job
{
    /**
     * سياسة إعادة المحاولة المخصصة لهذه المهمة.
     *
     * @var RetryPolicy|null
     */
    public ?RetryPolicy $retryPolicy = null;

    /**
     * الدالة الرئيسية التي تحتوي على منطق العمل.
     * يتم حقن الـ Container تلقائياً لتتمكن من استدعاء أي Service أو Repository تحتاجه.
     *
     * @param Container $container
     * @return void
     * @throws Exception
     */
    abstract public function handle(Container $container): void;

    /**
     * تحديد سياسة إعادة المحاولة.
     *
     * @return RetryPolicy
     */
    public function getRetryPolicy(): RetryPolicy
    {
        return $this->retryPolicy ?? new RetryPolicy(3, 60);
    }
}