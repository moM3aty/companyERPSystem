<?php
// Path: app/Core/Security/RateLimiter.php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Cache\CacheManager;
use App\Core\Exceptions\RateLimitException;

/**
 * Enterprise Rate Limiter Engine
 * محرك لتنظيم سرعة الطلبات (Throttling) لحماية الـ APIs ونظام تسجيل الدخول من الـ Brute Force والـ DDoS.
 */
class RateLimiter
{
    protected CacheManager $cache;

    /**
     * RateLimiter constructor.
     *
     * @param CacheManager $cache
     */
    public function __construct(CacheManager $cache)
    {
        $this->cache = $cache;
    }

    /**
     * فحص وتسجيل طلب جديد. إذا تم تجاوز الحد، يتم رمي استثناء.
     *
     * @param string $key المفتاح (مثال: IP Address أو User ID + اسم الـ Route)
     * @param int $maxAttempts الحد الأقصى للطلبات
     * @param int $decaySeconds مدة الحظر/إعادة التعيين بالثواني (مثال: 60 لـ دقيقة واحدة)
     * @return void
     * @throws RateLimitException
     */
    public function attempt(string $key, int $maxAttempts, int $decaySeconds = 60): void
    {
        $timerKey = $key . ':timer';
        
        // جلب عدد المحاولات الحالي
        $attempts = (int) $this->cache->get($key, 0);

        if ($attempts >= $maxAttempts) {
            // حساب الوقت المتبقي لفك الحظر
            $timer = (int) $this->cache->get($timerKey, 0);
            $retryAfter = $timer > 0 ? max(0, $timer - time()) : $decaySeconds;

            if ($retryAfter > 0) {
                throw new RateLimitException("Too many requests. Please try again later.", $retryAfter);
            } else {
                // انتهى وقت الحظر، تصفير العداد
                $this->clear($key);
                $attempts = 0;
            }
        }

        // زيادة المحاولات
        $attempts++;
        
        // إذا كانت هذه المحاولة الأولى، نبدأ المؤقت
        if ($attempts === 1) {
            $this->cache->set($timerKey, time() + $decaySeconds, $decaySeconds);
        }
        
        $this->cache->set($key, $attempts, $decaySeconds);
    }

    /**
     * تصفير العداد لعملية معينة (مثال: بعد نجاح تسجيل الدخول يتم تصفير المحاولات الخاطئة).
     *
     * @param string $key
     * @return void
     */
    public function clear(string $key): void
    {
        $this->cache->delete($key);
        $this->cache->delete($key . ':timer');
    }
    
    /**
     * جلب عدد المحاولات المتبقية قبل الحظر.
     *
     * @param string $key
     * @param int $maxAttempts
     * @return int
     */
    public function remaining(string $key, int $maxAttempts): int
    {
        $attempts = (int) $this->cache->get($key, 0);
        return max(0, $maxAttempts - $attempts);
    }
}