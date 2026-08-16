<?php
// Path: app/Core/Http/Middleware/IdempotencyMiddleware.php

declare(strict_types=1);

namespace App\Core\Http\Middleware;

use Closure;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\JsonResponse;
use App\Core\Api\IdempotencyManager;
use App\Core\Exceptions\ConflictException;

/**
 * Enterprise Idempotency Middleware
 * يستخدم لحماية مسارات الـ API الحساسة (مثل الدفع وتسجيل الفواتير) من الاستدعاء المزدوج 
 * الناتج عن الـ Retries من قبل العميل في حالة ضعف الشبكة.
 */
class IdempotencyMiddleware implements MiddlewareInterface
{
    protected IdempotencyManager $manager;

    public function __construct(IdempotencyManager $manager)
    {
        $this->manager = $manager;
    }

    public function process(Request $request, Closure $next): Response
    {
        // نطبق الحماية فقط على العمليات الحساسة (POST, PUT, PATCH, DELETE)
        if ($request->method() === 'GET' || $request->method() === 'OPTIONS') {
            return $next($request);
        }

        $idempotencyKey = $request->server('HTTP_IDEMPOTENCY_KEY');

        // إذا لم يُرسل العميل مفتاح، قد نتجاوزه أو نرفضه (هنا نسمح بالمرور للتبسيط، لكن في الأنظمة البنكية يُرفض).
        if (!$idempotencyKey) {
            return $next($request);
        }

        try {
            // تسجيل وحجز المفتاح
            $this->manager->ensureUniqueRequest($request);

            /** @var Response $response */
            $response = $next($request);

            // إذا فشل الطلب نتيجة خطأ في الإدخال (4xx)، نقوم بفك الحجز ليتمكن العميل من إصلاح الخطأ وإعادة الإرسال.
            if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
                $this->manager->release($idempotencyKey);
            }

            return $response;

        } catch (ConflictException $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 409);
        } catch (\Throwable $e) {
            // في حالة الانهيار الداخلي (500)، نُبقي الحجز لمنع التكرار الأعمى الذي قد يدمر الداتابيز.
            throw $e;
        }
    }
}