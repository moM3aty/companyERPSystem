<?php
// Path: app/Core/Integrations/IntegrationLogger.php

declare(strict_types=1);

namespace App\Core\Integrations;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Integration Logger
 * يقوم بتسجيل كل طلب (Request) واستجابة (Response) يتم بين الـ ERP وأي نظام خارجي.
 * حرج جداً لتتبع المشاكل مع البوابات الخارجية والدعم الفني.
 */
class IntegrationLogger
{
    protected DatabaseManager $db;
    protected string $table = 'integration_logs';


    /**
     * IntegrationLogger constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }


    /**
     * تسجيل عملية اتصال بالـ API الخارجي.
     *
     * @param string $provider اسم المزود (مثال: zatca)
     * @param string $endpoint المسار
     * @param string $method نوع الطلب
     * @param array $requestPayload البيانات المرسلة
     * @param array|string $responsePayload البيانات المستقبلة
     * @param int $statusCode كود الاستجابة (200, 400, 500)
     * @param float $executionTime الوقت المستغرق بالمللي ثانية
     * @param int|null $companyId
     * @return void
     */
    public function log(
        string $provider,
        string $endpoint,
        string $method,
        array $requestPayload,
        array|string $responsePayload,
        int $statusCode,
        float $executionTime,
        ?int $companyId = null
    ): void {
        $sql = "INSERT INTO {$this->table} 
                (company_id, provider, endpoint, method, request_payload, response_payload, status_code, execution_time_ms, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $requestJson = json_encode($this->maskSensitiveData($requestPayload), JSON_UNESCAPED_UNICODE);
        $responseJson = is_array($responsePayload) ? json_encode($responsePayload, JSON_UNESCAPED_UNICODE) : $responsePayload;

        $this->db->connection()->insert($sql, [
            $companyId,
            $provider,
            $endpoint,
            $method,
            $requestJson,
            $responseJson,
            $statusCode,
            $executionTime,
            date('Y-m-d H:i:s')
        ]);
    }


    /**
     * إخفاء البيانات الحساسة قبل تسجيلها في قاعدة البيانات.
     *
     * @param array $payload
     * @return array
     */
    protected function maskSensitiveData(array $payload): array
    {
        $sensitiveKeys = ['password', 'client_secret', 'api_key', 'token', 'cvv', 'card_number'];

        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->maskSensitiveData($value);
            } elseif (in_array(strtolower((string)$key), $sensitiveKeys, true)) {
                $payload[$key] = '********';
            }
        }

        return $payload;
    }
}