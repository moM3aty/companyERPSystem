<?php
// Path: config/cache.php

declare(strict_types=1);


/**
 * Enterprise Cache Configuration
 * Defines cache drivers and storage paths for high-performance data retrieval.
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    | Controls the default cache store that gets used while using the Cache manager.
    */
    'default' => env('CACHE_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    | When utilizing a RAM-based store like Redis, multiple applications may use the
    | same cache pool. A unique prefix avoids key collisions.
    */
    'prefix' => env('CACHE_PREFIX', 'nour_erp_cache_'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    | Here you may define all of the cache "stores" for your application.
    */
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path'   => dirname(__DIR__) . '/storage/cache',
        ],
        'redis' => [
            'driver'   => 'redis',
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port'     => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],
    ],
];