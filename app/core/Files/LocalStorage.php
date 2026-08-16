<?php
// Path: app/Core/Files/LocalStorage.php

declare(strict_types=1);

namespace App\Core\Files;

use RuntimeException;
use App\Core\Config\Config;

/**
 * Enterprise Local Storage Adapter
 * تطبيق لتخزين الملفات محلياً على سيرفر الـ ERP، متوافق مع הـ StorageInterface.
 */
class LocalStorage implements StorageInterface
{
    protected string $storagePath;
    protected string $baseUrl;

    public function __construct(Config $config)
    {
        $this->storagePath = rtrim($config->get('app.root'), '\/') . '/storage/app/public';
        $this->baseUrl = rtrim($config->get('app.url'), '/') . '/storage';

        if (!is_dir($this->storagePath)) {
            if (!mkdir($this->storagePath, 0755, true)) {
                throw new RuntimeException("Failed to create storage directory.");
            }
        }
    }

    protected function getFullPath(string $path): string
    {
        return $this->storagePath . '/' . trim(str_replace(['../', '..\\'], '', $path), '/');
    }

    public function put(string $path, string $contents): bool
    {
        $fullPath = $this->getFullPath($path);
        $dir = dirname($fullPath);
        
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return false;
        }

        return file_put_contents($fullPath, $contents, LOCK_EX) !== false;
    }

    public function get(string $path): ?string
    {
        $fullPath = $this->getFullPath($path);
        return file_exists($fullPath) ? file_get_contents($fullPath) : null;
    }

    public function exists(string $path): bool
    {
        return file_exists($this->getFullPath($path));
    }

    public function delete(string|array $paths): bool
    {
        $success = true;
        foreach ((array) $paths as $path) {
            $fullPath = $this->getFullPath($path);
            if (file_exists($fullPath) && !unlink($fullPath)) {
                $success = false;
            }
        }
        return $success;
    }

    public function url(string $path): string
    {
        return $this->baseUrl . '/' . trim(str_replace('\\', '/', $path), '/');
    }

    public function size(string $path): int
    {
        $fullPath = $this->getFullPath($path);
        return file_exists($fullPath) ? filesize($fullPath) : 0;
    }
}