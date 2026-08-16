<?php
// Path: app/Core/Files/LocalFileStorage.php

declare(strict_types=1);

namespace App\Core\Files;

use App\Core\Contracts\FileStorageInterface;
use App\Core\Config\Config;
use RuntimeException;

/**
 * Enterprise Local File Storage
 * التنفيذ الفعلي لعقد تخزين الملفات على القرص المحلي للسيرفر (Local File System).
 */
class LocalFileStorage implements FileStorageInterface
{
    /**
     * المسار الجذري لمجلد التخزين (مثال: /var/www/erp/storage/app/public)
     *
     * @var string
     */
    protected string $storagePath;

    /**
     * الرابط الأساسي للوصول للملفات عبر المتصفح (مثال: https://erp.com/storage)
     *
     * @var string
     */
    protected string $baseUrl;

    /**
     * LocalFileStorage constructor.
     *
     * @param Config $config
     * @throws RuntimeException
     */
    public function __construct(Config $config)
    {
        // إعداد مسار التخزين الافتراضي بناءً على مسار التطبيق الرئيسي
        $this->storagePath = rtrim($config->get('app.root'), '\/') . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public';
        
        // إعداد رابط المتصفح
        $this->baseUrl = rtrim($config->get('app.url'), '/') . '/storage';

        // التأكد من وجود المجلد الأساسي للتخزين
        if (!is_dir($this->storagePath)) {
            if (!mkdir($this->storagePath, 0755, true)) {
                throw new RuntimeException("Failed to create local storage directory at: {$this->storagePath}");
            }
        }
    }

    /**
     * الحصول على المسار الكامل للملف على السيرفر.
     *
     * @param string $path
     * @return string
     */
    protected function getFullPath(string $path): string
    {
        // حماية من ثغرات Directory Traversal (مثل ../../etc/passwd)
        $path = str_replace(['../', '..\\'], '', $path);
        return $this->storagePath . DIRECTORY_SEPARATOR . trim($path, '\/');
    }

    /**
     * @inheritDoc
     */
    public function exists(string $path): bool
    {
        return file_exists($this->getFullPath($path));
    }

    /**
     * @inheritDoc
     */
    public function get(string $path): ?string
    {
        if (!$this->exists($path)) {
            return null;
        }

        $content = file_get_contents($this->getFullPath($path));
        
        return $content !== false ? $content : null;
    }

    /**
     * @inheritDoc
     */
    public function put(string $path, string $contents): bool
    {
        $fullPath = $this->getFullPath($path);
        $directory = dirname($fullPath);

        // إنشاء المجلدات الفرعية تلقائياً إذا لم تكن موجودة (مثال: invoices/2026/08)
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                return false;
            }
        }

        // استخدام LOCK_EX لمنع تداخل الكتابة إذا حاول أكثر من مستخدم تعديل الملف في نفس اللحظة
        return file_put_contents($fullPath, $contents, LOCK_EX) !== false;
    }

    /**
     * @inheritDoc
     */
    public function delete(string|array $paths): bool
    {
        $paths = (array) $paths;
        $success = true;

        foreach ($paths as $path) {
            $fullPath = $this->getFullPath($path);
            
            if (file_exists($fullPath) && is_file($fullPath)) {
                if (!unlink($fullPath)) {
                    $success = false;
                }
            }
        }

        return $success;
    }

    /**
     * @inheritDoc
     */
    public function url(string $path): string
    {
        // تنظيف المسار ليتوافق مع روابط الويب (Forward Slashes)
        $cleanPath = trim(str_replace('\\', '/', $path), '/');
        
        return $this->baseUrl . '/' . $cleanPath;
    }

    /**
     * @inheritDoc
     */
    public function size(string $path): int
    {
        if (!$this->exists($path)) {
            return 0;
        }

        return (int) filesize($this->getFullPath($path));
    }
}