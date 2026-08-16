<?php
// Path: app/Core/Contracts/QueueInterface.php

declare(strict_types=1);

namespace App\Core\Contracts;

/**
 * Enterprise Queue Interface
 * Standardizes how jobs are pushed to background queues (Redis, Database, Beanstalkd).
 */
interface QueueInterface
{
    /**
     * Push a new job onto the queue.
     *
     * @param string|object $job The job class or object.
     * @param array $data Data required for the job.
     * @param string|null $queue The name of the specific queue.
     * @return mixed A job identifier.
     */
    public function push(string|object $job, array $data = [], ?string $queue = null): mixed;

    /**
     * Push a new job onto the queue after a specific delay.
     *
     * @param int $delay The delay in seconds.
     * @param string|object $job
     * @param array $data
     * @param string|null $queue
     * @return mixed A job identifier.
     */
    public function later(int $delay, string|object $job, array $data = [], ?string $queue = null): mixed;
}