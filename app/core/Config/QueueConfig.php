<?php
// Path: app/Core/Config/QueueConfig.php

declare(strict_types=1);

namespace App\Core\Config;

/**
 * Enterprise Queue Configuration
 * إعدادات محركات الطوابير (المهام في الخلفية). يتيح النظام التبديل بين Database أو Redis بسهولة.
 */
class QueueConfig
{
    public readonly string $default;
    public readonly array $connections;
    public readonly int $retryAfter;
    public readonly int $maxTries;

    /**
     * QueueConfig constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->default = $config->get('queue.default', 'database');
        
        $this->connections = $config->get('queue.connections', [
            'database' => [
                'driver' => 'database',
                'table' => 'jobs',
                'queue' => 'default',
                'retry_after' => 90, // الوقت بالثواني قبل إعادة إتاحة المهمة إذا فشل العامل (Worker)
            ],
            'redis' => [
                'driver' => 'redis',
                'connection' => 'default',
                'queue' => 'default',
                'retry_after' => 90,
                'block_for' => null,
            ],
        ]);

        $this->retryAfter = (int) $config->get('queue.retry_after', 90);
        $this->maxTries = (int) $config->get('queue.max_tries', 3);
    }
}