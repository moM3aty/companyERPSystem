<?php
// Path: app/Core/Integrations/WebhookManager.php

declare(strict_types=1);

namespace App\Core\Integrations;

use App\Core\Database\DatabaseManager;
use App\Core\Queue\JobDispatcher;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Webhook Manager
 * يدير إرسال الأحداث الداخلية إلى الأنظمة الخارجية بأمان.
 * يقوم بتوليد توقيع (Signature) لضمان أن المستقبل يعلم أن الطلب صادر من نظامنا ولم يتم التلاعب به.
 */
class WebhookManager
{
    protected DatabaseManager $db;
    protected ApiClient $apiClient;
    // protected JobDispatcher $dispatcher; // في النظام الكامل يتم وضعه في الـ Queue بدل الإرسال الفوري


    public function __construct(DatabaseManager $db, ApiClient $apiClient)
    {
        $this->db = $db;
        $this->apiClient = $apiClient;
    }

    /**
     * إطلاق Webhook استجابةً لحدث معين.
     *
     * @param string $eventName اسم الحدث (مثال: 'customer.updated')
     * @param array $payload البيانات
     * @param int $companyId
     * @return void
     */
    public function fire(string $eventName, array $payload, int $companyId): void
    {
        $webhooksData = $this->db->connection()->select(
            "SELECT * FROM webhooks WHERE company_id = ? AND event_name = ? AND is_active = 1",
            [$companyId, $eventName]
        );

        foreach ($webhooksData as $row) {
            $webhook = new Webhook($row);
            $this->dispatchToUrl($webhook, $payload);
        }
    }

    /**
     * إرسال الطلب مع التوقيع الآمن.
     *
     * @param Webhook $webhook
     * @param array $payload
     * @return void
     */
    protected function dispatchToUrl(Webhook $webhook, array $payload): void
    {
        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);
        
        // إنشاء توقيع آمن (HMAC SHA256)
        $signature = hash_hmac('sha256', (string)$jsonPayload, (string)$webhook->getAttribute('secret_key'));

        $headers = [
            'Content-Type' => 'application/json',
            'X-ERP-Signature' => $signature, // المستقبل سيستخدم نفس السر ليتأكد من التوقيع
            'X-ERP-Event' => $webhook->getAttribute('event_name'),
        ];

        try {
            // في النظام الحقيقي يجب أن توضع هذه الخطوة في الـ Queue لتجنب إبطاء العملية الحالية
            $this->apiClient->send('webhook', 'POST', (string)$webhook->getAttribute('target_url'), $headers, $payload);
        } catch (\Throwable $e) {
            // الفشل في إرسال الـ Webhook لا يجب أن يوقف العملية الأساسية
            // سيتم تسجيله تلقائياً بواسطة ApiClient
        }
    }
}