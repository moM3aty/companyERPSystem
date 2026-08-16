<?php
// Path: app/Core/Api/ApiResponse.php

declare(strict_types=1);

namespace App\Core\Api;

use App\Core\Http\JsonResponse;
use App\Core\Database\Pagination;

/**
 * Enterprise API Response Formatter
 * يقوم بتوحيد شكل جميع الاستجابات الناجحة الصادرة من النظام لتكون مطابقة لمعايير REST APIs العالمية.
 */
class ApiResponse
{
    /**
     * استجابة نجاح عامة مع بيانات.
     *
     * @param mixed $data البيانات المراد إرجاعها
     * @param string $message رسالة توضيحية اختيارية
     * @param int $statusCode كود حالة HTTP (الافتراضي 200)
     * @param array $meta أي بيانات إضافية للـ Meta
     * @return JsonResponse
     */
    public static function success(mixed $data = null, string $message = 'Operation successful.', int $statusCode = 200, array $meta = []): JsonResponse
    {
        $payload = [
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ];

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        return new JsonResponse($payload, $statusCode);
    }

    /**
     * استجابة نجاح عند إنشاء عنصر جديد (مثال: إنشاء فاتورة أو عميل جديد).
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    public static function created(mixed $data = null, string $message = 'Resource created successfully.'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * استجابة نجاح مخصصة للبيانات المقسمة لصفحات (Pagination).
     *
     * @param Pagination $pagination كائن الـ Pagination
     * @param string $message
     * @return JsonResponse
     */
    public static function paginated(Pagination $pagination, string $message = 'Data retrieved successfully.'): JsonResponse
    {
        $payload = [
            'status'  => 'success',
            'message' => $message,
            'data'    => $pagination->items,
            'meta'    => [
                'pagination' => [
                    'total'        => $pagination->total,
                    'per_page'     => $pagination->perPage,
                    'current_page' => $pagination->currentPage,
                    'last_page'    => $pagination->lastPage,
                    'has_more'     => $pagination->hasMorePages()
                ]
            ]
        ];

        return new JsonResponse($payload, 200);
    }

    /**
     * استجابة نجاح بدون أي بيانات (مثال: بعد نجاح عملية حذف).
     *
     * @return JsonResponse
     */
    public static function noContent(): JsonResponse
    {
        // 204 No Content لا يقبل أي Body في الاستجابة
        return new JsonResponse(null, 204);
    }
}