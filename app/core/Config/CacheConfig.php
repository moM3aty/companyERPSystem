<?php
// Path: app/Core/Config/CacheConfig.php

declare(strict_types=1);

namespace App\Core\Config;

/**
 * Enterprise Cache Configuration
 * إعدادات محركات الـ Cache والتخزين المؤقت لرفع أداء النظام وتقليل استعلامات قاعدة البيانات.
 */
class CacheConfig
{
    public readonly string $defaultDriver;
    public readonly string $prefix;
    public readonly string $fileCachePath;

    /**
     * CacheConfig constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->defaultDriver = $config->get('cache.default', 'file'); // file, redis, array
        $this->prefix = $config->get('cache.prefix', 'erp_cache_');
        
        $appRoot = $config->get('app.root', dirname(__DIR__, 3));
        $this->fileCachePath = $config->get('cache.stores.file.path', $appRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache');
    }

    /**
     * Get the default cache configurations.
     *
     * @param string $basePath
     * @return array
     */
    public static function getDefaults(string $basePath): array
    {
        return [
            'default' => 'file',
            'prefix' => 'erp_cache_',
            'stores' => [
                'file' => [
                    'driver' => 'file',
                    'path' => $basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache',
                ],
                'redis' => [
                    'driver' => 'redis',
                    'connection' => 'cache',
                ],
            ],
        ];
    }
}