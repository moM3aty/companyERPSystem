<?php
// Path: app/Core/Config/DatabaseConfig.php

declare(strict_types=1);

namespace App\Core\Config;

/**
 * Enterprise Database Configuration
 * Holds strongly-typed database connection credentials for Production.
 */
class DatabaseConfig
{
    public readonly string $host;
    public readonly string $username;
    public readonly string $password;
    public readonly string $database;
    public readonly string $charset;
    public readonly string $collation;

    public function __construct(Config $config)
    {
        $this->host = $config->get('database.host', 'localhost');
        $this->username = $config->get('database.username', 'u582652079_erpAdmin');
        $this->password = $config->get('database.password', '6s*Tbt+j@QW>');
        $this->database = $config->get('database.name', 'u582652079_erp');
        $this->charset = $config->get('database.charset', 'utf8mb4');
        $this->collation = $config->get('database.collation', 'utf8mb4_unicode_ci');
    }

    public static function getDefaults(): array
    {
        return [
            // بيانات قاعدة بيانات الاستضافة الفعلية
            'host' => 'localhost',
            'username' => 'u582652079_erpAdmin',
            'password' => '6s*Tbt+j@QW>',
            'name' => 'u582652079_erp',
            
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            
            'options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]
        ];
    }
}