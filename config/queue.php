<?php
// Path: config/queue.php

declare(strict_types=1);


/**
 * Enterprise Queue Configuration
 * For asynchronous background jobs (e.g., sending emails, ZATCA e-invoicing sync, heavy reports).
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection
    |--------------------------------------------------------------------------
    */
    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    */
    'connections' => [
        'database' => [
            'driver'      => 'database',
            'table'       => 'jobs',
            'queue'       => 'default',
            'retry_after' => 90,
        ],
        'redis' => [
            'driver'      => 'redis',
            'connection'  => 'default',
            'queue'       => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for'   => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    */
    'failed' => [
        'driver'   => 'database',
        'database' => 'mysql',
        'table'    => 'failed_jobs',
    ],
];