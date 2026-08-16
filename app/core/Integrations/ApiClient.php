<?php
// Path: app/Core/Integrations/ApiClient.php

declare(strict_types=1);

namespace App\Core\Integrations;

use App\Core\Exceptions\IntegrationException;

/**
 * Enterprise API Client
 * محرك اتصال قوي يعتمد على cURL، مزود بنظام إعادة المحاولة (Retry with Exponential Backoff).
 * يغنيك عن استخدام مكتبات خارجية ضخمة مثل Guzzle ويوفر أداءً أعلى.
 */
class ApiClient
{
    protected IntegrationLogger $logger;
    protected int $maxRetries = 3;
    protected int $timeout = 30; // بالثواني


    public function __construct(IntegrationLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * إرسال الطلب مع دعم إعادة المحاولة آلياً في حال فشل السيرفر الخارجي.
     *
     * @param string $provider
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param array $payload
     * @return array
     * @throws IntegrationException
     */
    public function send(string $provider, string $method, string $url, array $headers = [], array $payload = []): array
    {
        $attempt = 0;
        $response = null;
        $statusCode = 0;
        $error = '';

        $method = strtoupper($method);
        $formattedHeaders = $this->formatHeaders($headers);
        $jsonData = empty($payload) ? '' : json_encode($payload, JSON_UNESCAPED_UNICODE);

        while ($attempt < $this->maxRetries) {
            $startTime = microtime(true);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $formattedHeaders);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            
            if ($method !== 'GET' && $jsonData !== '') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            }

            $result = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            
            curl_close($ch);

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            // Log the attempt
            $this->logger->log($provider, $url, $method, $payload, $result ?: $error, $statusCode, $executionTime);

            // النجاح (2xx)
            if ($statusCode >= 200 && $statusCode < 300) {
                return json_decode((string) $result, true) ?? ['raw_response' => $result];
            }

            // أخطاء العميل (4xx) - لا نعيد المحاولة لأن الخطأ من بياناتنا
            if ($statusCode >= 400 && $statusCode < 500) {
                throw new IntegrationException("Client error ({$statusCode}) from {$provider}: " . ($result ?: $error), $statusCode);
            }

            // أخطاء السيرفر (5xx) أو فشل الاتصال - نعيد المحاولة
            $attempt++;
            if ($attempt < $this->maxRetries) {
                // Exponential backoff: 1s, 2s, 4s...
                sleep((int) pow(2, $attempt - 1));
            }
        }

        throw new IntegrationException("Failed to connect to {$provider} after {$this->maxRetries} attempts. Last status: {$statusCode}. Error: {$error}", 502);
    }

    /**
     * تنسيق الـ Headers للصيغة التي يقبلها cURL.
     *
     * @param array $headers
     * @return array
     */
    protected function formatHeaders(array $headers): array
    {
        $formatted = [];
        foreach ($headers as $key => $value) {
            $formatted[] = "{$key}: {$value}";
        }
        
        // Ensure JSON content type if not explicitly overridden
        $hasContentType = false;
        foreach ($formatted as $header) {
            if (stripos($header, 'Content-Type') !== false) {
                $hasContentType = true;
                break;
            }
        }
        
        if (!$hasContentType) {
            $formatted[] = 'Content-Type: application/json';
        }

        return $formatted;
    }
}