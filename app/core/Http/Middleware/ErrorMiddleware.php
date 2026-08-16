<?php
// Path: app/Core/Http/Middleware/ErrorMiddleware.php

declare(strict_types=1);

namespace App\Core\Http\Middleware;

use Closure;
use Throwable;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Api\ApiError;
use App\Core\Exceptions\ValidationException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Error Formatting Middleware
 * يصطاد الأخطاء قبل وصولها للـ Kernel النهائي لعمل (Formatting) متخصص للـ API.
 */
class ErrorMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (ValidationException $e) {
            // التعامل مع أخطاء التحقق من البيانات وتوحيد مخرجاتها
            if ($request->ajax() || str_contains($request->server('HTTP_ACCEPT', ''), 'application/json')) {
                return ApiError::validation($e->getErrors(), $e->getMessage());
            }
            throw $e; // نتركه للـ Kernel لو كان طلب ويب عادي

        } catch (NotFoundException $e) {
            if ($request->ajax() || str_contains($request->server('HTTP_ACCEPT', ''), 'application/json')) {
                return ApiError::notFound($e->getMessage());
            }
            throw $e;

        } catch (BusinessException $e) {
            // قواعد العمل (رصيد غير كافي، قيد غير متزن)
            if ($request->ajax() || str_contains($request->server('HTTP_ACCEPT', ''), 'application/json')) {
                return ApiError::error($e->getMessage(), $e->getCode() ?: 422);
            }
            throw $e;

        } catch (Throwable $e) {
            // أي خطأ آخر لم تتم معالجته، سيتم تمريره للأعلى ليتعامل معه الـ Kernel الرئيسي.
            throw $e; 
        }
    }
}