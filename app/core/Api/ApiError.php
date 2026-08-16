<?php
// Path: app/Core/Api/ApiError.php

declare(strict_types=1);

namespace App\Core\Api;

use App\Core\Http\JsonResponse;

/**
 * Enterprise API Error Formatter
 * يضمن أن جميع رسائل الخطأ تخرج بصيغة هيكلية واحدة ليسهل على تطبيقات الموبايل والـ Frontend معالجتها.
 */
class ApiError
{
    /**
     * استجابة خطأ عامة.
     *
     * @param string $message رسالة الخطأ
     * @param int $statusCode كود الخطأ (الافتراضي 400 Bad Request)
     * @param array $errors تفاصيل إضافية عن الأخطاء (إن وجدت)
     * @param array $trace التتبع (لبيئة التطوير فقط)
     * @return JsonResponse
     */
    public static function error(string $message, int $statusCode = 400, array $errors = [], array $trace = []): JsonResponse
    {
        $payload = [
            'status'  => 'error',
            'message' => $message,
            'code'    => $statusCode,
        ];

        if (!empty($errors)) {
            $payload['errors'] = $errors;
        }

        if (!empty($trace)) {
            $payload['trace'] = $trace; // يجب أن تكون فارغة في بيئة الـ Production
        }

        return new JsonResponse($payload, $statusCode);
    }

    /**
     * استجابة فشل التحقق من صحة البيانات (Validation).
     *
     * @param array $validationErrors مصفوفة الأخطاء القادمة من הـ Validator
     * @param string $message
     * @return JsonResponse
     */
    public static function validation(array $validationErrors, string $message = 'The given data was invalid.'): JsonResponse
    {
        return self::error($message, 422, $validationErrors);
    }

    /**
     * استجابة في حالة عدم وجود صلاحية أو مستخدم غير مسجل دخول.
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function unauthorized(string $message = 'Unauthenticated.'): JsonResponse
    {
        return self::error($message, 401);
    }

    /**
     * استجابة في حالة أن المستخدم مسجل دخول ولكن ليس لديه صلاحية لهذا الإجراء.
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function forbidden(string $message = 'Access Denied: You do not have permission to access this resource.'): JsonResponse
    {
        return self::error($message, 403);
    }

    /**
     * استجابة في حالة عدم العثور على المورد المطلوب (مثال: عميل غير موجود).
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return self::error($message, 404);
    }

    /**
     * استجابة خطأ داخلي في السيرفر (Internal Server Error).
     *
     * @param string $message
     * @param array $trace
     * @return JsonResponse
     */
    public static function serverError(string $message = 'Internal Server Error.', array $trace = []): JsonResponse
    {
        return self::error($message, 500, [], $trace);
    }
}