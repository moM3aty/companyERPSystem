<?php
// Path: app/Core/Config/StorageConfig.php

declare(strict_types=1);

namespace App\Core\Config;

/**
 * Enterprise Storage Configuration
 * إعدادات التخزين للملفات (مرفقات الفواتير، صور المنتجات). يدعم التخزين المحلي أو السحابي.
 */
class StorageConfig
{
    public readonly string $defaultDisk;
    public readonly array $disks;

    /**
     * StorageConfig constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->defaultDisk = $config->get('filesystems.default', 'local');
        
        $appRoot = $config->get('app.root');
        
        $this->disks = $config->get('filesystems.disks', [
            'local' => [
                'driver' => 'local',
                'root' => $appRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public',
                'url' => $config->get('app.url') . '/storage',
                'visibility' => 'public',
            ],
            's3' => [
                'driver' => 's3',
                'key' => $config->get('filesystems.s3.key', ''),
                'secret' => $config->get('filesystems.s3.secret', ''),
                'region' => $config->get('filesystems.s3.region', 'us-east-1'),
                'bucket' => $config->get('filesystems.s3.bucket', ''),
                'url' => $config->get('filesystems.s3.url', ''),
                'endpoint' => $config->get('filesystems.s3.endpoint', ''),
                'visibility' => 'private', // الافتراضي للملفات المالية
            ],
        ]);
    }
}