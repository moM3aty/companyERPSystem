<?php
// Path: app/Core/Cache/CacheManager.php

declare(strict_types=1);

namespace App\Core\Cache;

use Closure;
use App\Core\Config\Config;
use App\Core\Contracts\CacheInterface;

/**
 * Enterprise Cache Manager
 * يوفر الواجهة الأساسية للتعامل مع الـ Cache ويقوم تلقائياً بتوجيه الطلب لمحرك الـ Cache المناسب.
 */
class CacheManager
{
    protected CacheInterface $driver;
    protected Config $config;

    /**
     * CacheManager constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->resolveDriver();
    }

    /**
     * تحديد وتشغيل محرك الـ Cache المناسب بناءً على الإعدادات.
     * (حالياً يدعم الـ FileCache ومجهز لـ Redis مستقبلاً).
     *
     * @return void
     */
    protected function resolveDriver(): void
    {
        // تحديد مسار تخزين ملفات الكاش (افتراضياً storage/framework/cache)
        $appRoot = $this->config->get('app.root');
        $cacheDir = $appRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache';
        
        $this->driver = new FileCache($cacheDir);
    }

    /**
     * جلب قيمة من الـ Cache.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->driver->get($key, $default);
    }

    /**
     * تخزين قيمة في الـ Cache.
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl (الوقت بالثواني، مثلاً 3600 لمدة ساعة)
     * @return bool
     */
    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        return $this->driver->set($key, $value, $ttl);
    }

    /**
     * التحقق من وجود مفتاح في الـ Cache.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->driver->has($key);
    }

    /**
     * حذف مفتاح معين.
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return $this->driver->delete($key);
    }

    /**
     * مسح كل الـ Cache.
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->driver->clear();
    }

    /**
     * الدالة الأذكى لتحسين الأداء: تجلب القيمة، أو تنفذ الكود وتحفظه لو مش موجود.
     *
     * @param string $key
     * @param int $ttl
     * @param Closure $callback
     * @return mixed
     */
    public function remember(string $key, int $ttl, Closure $callback): mixed
    {
        return $this->driver->remember($key, $ttl, $callback);
    }
}