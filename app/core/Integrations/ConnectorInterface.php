<?php
// Path: app/Core/Integrations/ConnectorInterface.php

declare(strict_types=1);

namespace App\Core\Integrations;

/**
 * Enterprise Connector Interface
 * واجهة قياسية يجب أن يلتزم بها أي كلاس يقوم بالربط مع نظام خارجي (مثال: ZatcaConnector, StripeConnector).
 * يضمن توحيد طريقة المصادقة وإرسال الطلبات.
 */
interface ConnectorInterface
{

    /**
     * المصادقة مع النظام الخارجي (جلب Token مثلاً).
     *
     * @return bool
     */
    public function authenticate(): bool;

    /**
     * جلب الترويسات (Headers) المطلوبة للاتصال (مثل Authorization).
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * إرسال طلب إلى النظام الخارجي.
     *
     * @param string $method (GET, POST, PUT, DELETE)
     * @param string $endpoint المسار (مثال: /v1/invoices)
     * @param array $payload البيانات المرسلة
     * @return array الاستجابة
     * @throws \App\Core\Exceptions\IntegrationException
     */
    public function sendRequest(string $method, string $endpoint, array $payload = []): array;
}