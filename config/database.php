<?php
// Path: config/database.php

declare(strict_types=1);


/**
 * Enterprise Database Configuration
 * Handles primary connections, read-replicas, and PDO attributes for Hostinger & production.
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    */
    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    | Here are each of the database connections setup for your application.
    */
    'connections' => [
        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '127.0.0.1'),
            'port'      => env('DB_PORT', '3306'),
            'database'  => env('DB_DATABASE', 'u582652079_nour_erp'),
            'username'  => env('DB_USERNAME', 'u582652079_nour_user'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'options'   => [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],
        'mysql_read' => [
            'driver'    => 'mysql',
            'host'      => env('DB_READ_HOST', env('DB_HOST', '127.0.0.1')),
            'port'      => env('DB_PORT', '3306'),
            'database'  => env('DB_DATABASE', 'u582652079_nour_erp'),
            'username'  => env('DB_READ_USERNAME', env('DB_USERNAME', 'u582652079_nour_user')),
            'password'  => env('DB_READ_PASSWORD', env('DB_PASSWORD', '')),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],
    ],
];