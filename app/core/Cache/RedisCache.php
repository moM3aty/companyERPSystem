<?php
// Path: app/Core/Cache/RedisCache.php

declare(strict_types=1);

namespace App\Core\Cache;

use App\Core\Contracts\CacheInterface;
use Closure;
use RuntimeException;

/**
 * Enterprise Redis Cache Adapter
 * يوفر أعلى أداء للـ Caching في الـ ERP عبر الذاكرة باستخدام إضافة PHP Redis.
 */
class RedisCache implements CacheInterface
{
    protected \Redis $redis;
    protected string $prefix;

    public function __construct(string $host = '127.0.0.1', int $port = 6379, string $prefix = 'erp:')
    {
        if (!extension_loaded('redis')) {
            throw new RuntimeException("Redis extension is not loaded.");
        }

        $this->redis = new \Redis();
        $this->redis->connect($host, $port);
        $this->prefix = $prefix;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->redis->get($this->prefix . $key);
        return $value !== false ? unserialize($value) : $default;
    }

    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        $serialized = serialize($value);
        $fullKey = $this->prefix . $key;

        if ($ttl > 0) {
            return $this->redis->setex($fullKey, $ttl, $serialized);
        }
        return $this->redis->set($fullKey, $serialized);
    }

    public function has(string $key): bool
    {
        return $this->redis->exists($this->prefix . $key) > 0;
    }

    public function delete(string $key): bool
    {
        return $this->redis->del($this->prefix . $key) > 0;
    }

    public function clear(): bool
    {
        // مسح المفاتيح التي تبدأ بالـ Prefix فقط للحفاظ على استقرار الخدمات الأخرى في نفس الـ Redis
        $keys = $this->redis->keys($this->prefix . '*');
        if (!empty($keys)) {
            $this->redis->del($keys);
        }
        return true;
    }

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
}