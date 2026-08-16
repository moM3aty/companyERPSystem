<?php
// Path: app/Core/Api/IdempotencyManager.php

declare(strict_types=1);

namespace App\Core\Api;

use App\Core\Cache\CacheManager;
use App\Core\Http\Request;
use App\Core\Exceptions\ConflictException;

/**
 * Enterprise Idempotency Manager
 * يمنع تكرار تنفيذ العمليات الحساسة (مثل الدفع أو ترحيل القيود) إذا تم إرسال نفس الطلب مرتين
 * نتيجة لضعف الاتصال بالإنترنت عند العميل (Double Submit Problem).
 */
class IdempotencyManager
{
    protected CacheManager $cache;

    public function __construct(CacheManager $cache)
    {
        $this->cache = $cache;
    }

    /**
     * التحقق من مفتاح الـ Idempotency، وتسجيله إذا كان جديداً.
     *
     * @param Request $request
     * @return void
     * @throws ConflictException
     */
    public function ensureUniqueRequest(Request $request): void
    {
        $idempotencyKey = $request->server('HTTP_IDEMPOTENCY_KEY');

        if (empty($idempotencyKey)) {
            // في بعض الأنظمة لا يكون إجبارياً، ولكن إن وجد يتم التعامل معه.
            // للتطبيق الإجباري، يمكن رمي خطأ هنا.
            return;
        }

        $cacheKey = "idempotency_{$idempotencyKey}";

        if ($this->cache->has($cacheKey)) {
            throw new ConflictException(
                "Idempotency Conflict: A request with this key is already processing or has been processed.",
                409
            );
        }

        // تخزين المفتاح لمدة 24 ساعة
        $this->cache->set($cacheKey, 'processing', 86400);
    }

    /**
     * إزالة المفتاح (مثلاً في حال فشل الطلب نتيجة خطأ في الـ Validation ونريد السماح بإعادة المحاولة).
     *
     * @param string $idempotencyKey
     * @return void
     */
    public function release(string $idempotencyKey): void
    {
        $this->cache->delete("idempotency_{$idempotencyKey}");
    }
}