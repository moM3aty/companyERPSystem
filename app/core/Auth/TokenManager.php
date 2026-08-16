<?php
// Path: app/Core/Auth/TokenManager.php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Config\Config;
use App\Core\Exceptions\AuthenticationException;

/**
 * Enterprise JWT Token Manager
 * Generates and verifies JSON Web Tokens (JWT) natively without external dependencies.
 * Critical for Mobile Apps and Headless API integration.
 */
class TokenManager
{
    protected string $secretKey;
    protected int $lifetime;

    /**
     * TokenManager constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->secretKey = $config->get('security.encryption_key', 'DEFAULT_FALLBACK_SECRET_KEY_FOR_JWT');
        $this->lifetime = (int) $config->get('security.session_lifetime', 7200);
    }

    /**
     * Generate a new JWT token for a user.
     *
     * @param AuthUser $user
     * @return string
     */
    public function generateToken(AuthUser $user): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        $issuedAt = time();
        $expirationTime = $issuedAt + $this->lifetime;

        $payload = json_encode([
            'iss' => 'erp_system',
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'usr' => $user->toArray()
        ]);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secretKey, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Verify a JWT token and extract the user.
     *
     * @param string $token
     * @return AuthUser|null
     * @throws AuthenticationException
     */
    public function verifyToken(string $token): ?AuthUser
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new AuthenticationException('Invalid token format.', 401);
        }

        [$base64UrlHeader, $base64UrlPayload, $base64UrlSignature] = $parts;

        // Verify Signature
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secretKey, true);
        $expectedSignature = $this->base64UrlEncode($signature);

        if (!hash_equals($expectedSignature, $base64UrlSignature)) {
            throw new AuthenticationException('Token signature verification failed.', 401);
        }

        // Verify Expiration
        $payload = json_decode($this->base64UrlDecode($base64UrlPayload), true);
        
        if (!$payload || !isset($payload['exp']) || $payload['exp'] < time()) {
            throw new AuthenticationException('Token has expired.', 401);
        }

        if (!isset($payload['usr'])) {
            throw new AuthenticationException('Invalid token payload.', 401);
        }

        $userData = $payload['usr'];

        return new AuthUser(
            (int) $userData['id'],
            (int) $userData['company_id'],
            (string) $userData['username'],
            (string) $userData['email'],
            $userData['employee_id'] ? (int) $userData['employee_id'] : null,
            (string) ($userData['language'] ?? 'ar'),
            (string) ($userData['timezone'] ?? 'Asia/Riyadh')
        );
    }

    /**
     * Encode data to Base64URL safely.
     *
     * @param string $data
     * @return string
     */
    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decode data from Base64URL safely.
     *
     * @param string $data
     * @return string
     */
    protected function base64UrlDecode(string $data): string
    {
        $paddedData = str_pad($data, strlen($data) % 4, '=', STR_PAD_RIGHT);
        return base64_decode(strtr($paddedData, '-_', '+/'));
    }
}