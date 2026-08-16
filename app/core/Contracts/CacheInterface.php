<?php
// Path: app/Core/Contracts/CacheInterface.php

declare(strict_types=1);

namespace App\Core\Contracts;

use Closure;

/**
 * Enterprise Cache Interface
 * يحدد العقد الأساسي الذي يجب أن تلتزم به أي أداة Caching (ملفات، Redis، Memcached).
 */
interface CacheInterface
{
    /**
     * جلب قيمة من الـ Cache.
     *
     * @param string $key مفتاح البحث
     * @param mixed $default القيمة الافتراضية إذا لم يكن موجوداً
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * تخزين قيمة في الـ Cache.
     *
     * @param string $key المفتاح
     * @param mixed $value القيمة
     * @param int $ttl الوقت المتبقي بالثواني (Time to live). القيمة 0 تعني تخزين دائم.
     * @return bool
     */
    public function set(string $key, mixed $value, int $ttl = 0): bool;

    /**
     * التحقق من وجود مفتاح في الـ Cache وحالته سارية.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * حذف قيمة معينة من الـ Cache.
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool;

    /**
     * مسح جميع محتويات الـ Cache.
     *
     * @return bool
     */
    public function clear(): bool;

    /**
     * جلب قيمة، وإن لم تكن موجودة يقوم بتنفيذ دالة معينة وتخزين نتيجتها.
     * (من أهم وأكثر الدوال استخداماً لتحسين الأداء).
     *
     * @param string $key
     * @param int $ttl
     * @param Closure $callback
     * @return mixed
     */
    public function remember(string $key, int $ttl, Closure $callback): mixed;
}