<?php
// Path: app/Core/Integrations/OAuthClient.php

declare(strict_types=1);

namespace App\Core\Integrations;

use App\Core\Cache\CacheManager;
use App\Core\Exceptions\IntegrationException;

/**
 * Enterprise OAuth2 Client
 * متخصص في جلب وإدارة التوكنز (Tokens) للأنظمة التي تتطلب مصادقة OAuth2 (Client Credentials).
 * يقوم بتخزين التوكن في الـ Cache حتى لا يتم طلب توكن جديد مع كل استعلام.
 */
class OAuthClient
{
    protected ApiClient $apiClient;
    protected CacheManager $cache;


    public function __construct(ApiClient $apiClient, CacheManager $cache)
    {
        $this->apiClient = $apiClient;
        $this->cache = $cache;
    }

    /**
     * جلب توكن نشط (من الـ Cache أو السيرفر).
     *
     * @param string $provider اسم المزود (للتخزين في الـ Cache)
     * @param string $tokenUrl رابط جلب التوكن
     * @param string $clientId
     * @param string $clientSecret
     * @param string $scope
     * @return string التوكن (Access Token)
     * @throws IntegrationException
     */
    public function getAccessToken(string $provider, string $tokenUrl, string $clientId, string $clientSecret, string $scope = ''): string
    {
        $cacheKey = "oauth_token_{$provider}_{$clientId}";

        // 1. التحقق من الكاش
        $cachedToken = $this->cache->get($cacheKey);
        if ($cachedToken) {
            return (string) $cachedToken;
        }

        // 2. طلب توكن جديد
        $payload = [
            'grant_type'    => 'client_credentials',
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
        ];

        if (!empty($scope)) {
            $payload['scope'] = $scope;
        }

        // إرسال الطلب بصيغة Form URL Encoded
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
        
        // التحايل على الـ ApiClient لإرسال البيانات بصيغة x-www-form-urlencoded
        $formData = http_build_query($payload);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
        
        $responseRaw = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode !== 200) {
            throw new IntegrationException("Failed to retrieve OAuth token from {$provider}. Status: {$statusCode}");
        }

        $responseData = json_decode((string)$responseRaw, true);

        if (!isset($responseData['access_token'])) {
            throw new IntegrationException("Invalid OAuth response from {$provider}: Missing access_token.");
        }

        // 3. تخزين التوكن في الكاش (نطرح دقيقة من وقت الانتهاء لتجنب المشاكل)
        $expiresIn = (int) ($responseData['expires_in'] ?? 3600);
        $ttl = max(0, $expiresIn - 60);

        $this->cache->set($cacheKey, $responseData['access_token'], $ttl);

        return $responseData['access_token'];
    }
}