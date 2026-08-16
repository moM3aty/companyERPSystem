<?php
// Path: app/Core/Config/AppConfig.php

declare(strict_types=1);

namespace App\Core\Config;

/**
 * Enterprise Application Configuration
 */
class AppConfig
{
    public readonly string $name;
    public readonly string $env;
    public readonly string $url;
    public readonly string $version;
    public readonly string $basePath;

    public function __construct(Config $config)
    {
        $this->name = $config->get('app.name', 'ERP Pro System');
        $this->env = $config->get('app.env', 'production');
        $this->url = $config->get('app.url', 'https://nourtrust.com/ERP/public');
        $this->version = $config->get('app.version', '2.0.0');
        $this->basePath = $config->get('app.root', dirname(__DIR__, 3));
    }

    public static function getDefaults(string $basePath): array
    {
        return [
            'name' => 'ERP Pro System',
            'env' => 'production',
            'root' => $basePath,
            // تثبيت الرابط الأساسي لتجنب أخطاء توجيه المجلدات الفرعية
            'url' => 'https://nourtrust.com/ERP/public',
            'version' => '2.0.0'
        ];
    }

    public function isProduction(): bool
    {
        return $this->env === 'production';
    }

    public function isDevelopment(): bool
    {
        return $this->env === 'development';
    }
}