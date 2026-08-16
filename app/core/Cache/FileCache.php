<?php
// Path: app/Core/Cache/FileCache.php

declare(strict_types=1);

namespace App\Core\Cache;

use Closure;
use RuntimeException;
use App\Core\Contracts\CacheInterface;

/**
 * Enterprise File-Based Cache
 * يقوم بتخزين البيانات في ملفات على السيرفر كحل سريع وفعال بدون الحاجة لسطب خدمات خارجية زي Redis.
 */
class FileCache implements CacheInterface
{
    protected string $cacheDir;

    /**
     * FileCache constructor.
     *
     * @param string $cacheDir مسار مجلد التخزين
     */
    public function __construct(string $cacheDir)
    {
        $this->cacheDir = rtrim($cacheDir, '\/');

        if (!is_dir($this->cacheDir)) {
            if (!mkdir($this->cacheDir, 0755, true)) {
                throw new RuntimeException("Failed to create cache directory at: {$this->cacheDir}");
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $path = $this->getFilePath($key);

        if (!file_exists($path)) {
            return $default;
        }

        $content = file_get_contents($path);
        
        if ($content === false) {
            return $default;
        }

        $data = unserialize($content);

        // التحقق مما إذا كانت البيانات قد انتهت صلاحيتها (Expired)
        if ($data['expiration'] !== 0 && time() > $data['expiration']) {
            $this->delete($key);
            return $default;
        }

        return $data['value'];
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        $path = $this->getFilePath($key);
        
        $expiration = $ttl > 0 ? time() + $ttl : 0;
        
        $data = [
            'expiration' => $expiration,
            'value'      => $value,
        ];

        $content = serialize($data);

        // استخدام LOCK_EX يضمن عدم حدوث تضارب إذا حاول أكثر من مستخدم الكتابة في نفس الوقت
        return file_put_contents($path, $content, LOCK_EX) !== false;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        $path = $this->getFilePath($key);
        
        if (file_exists($path)) {
            return unlink($path);
        }
        
        return false;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $files = glob($this->cacheDir . '/*');
        
        $success = true;
        foreach ($files as $file) {
            if (is_file($file)) {
                if (!unlink($file)) {
                    $success = false;
                }
            }
        }

        return $success;
    }

    /**
     * @inheritDoc
     */
    public function remember(string $key, int $ttl, Closure $callback): mixed
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        
        $this->set($key, $value, $ttl);

        return $value;
    }

    /**
     * توليد مسار الملف بشكل آمن باستخدام التشفير لمنع مسارات الاختراق.
     *
     * @param string $key
     * @return string
     */
    protected function getFilePath(string $key): string
    {
        // نستخدم sha1 لتوليد اسم ملف آمن وتجنب وجود مسافات أو رموز غير مسموحة في اسم الملف
        $fileName = sha1($key) . '.cache';
        
        return $this->cacheDir . DIRECTORY_SEPARATOR . $fileName;
    }
}