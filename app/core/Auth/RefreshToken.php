<?php
// Path: app/Core/Auth/RefreshToken.php

declare(strict_types=1);

namespace App\Core\Auth;

use JsonSerializable;

/**
 * Enterprise DTO: Refresh Token
 * يستخدم لتجديد الـ Access Token عند انتهائه دون الحاجة لتسجيل الدخول مرة أخرى.
 */
class RefreshToken implements JsonSerializable
{
    public readonly string $token;
    public readonly int $expiresIn;

    public function __construct(string $token, int $expiresIn)
    {
        $this->token = $token;
        $this->expiresIn = $expiresIn;
    }

    public function jsonSerialize(): array
    {
        return [
            'refresh_token' => $this->token,
            'refresh_expires_in' => $this->expiresIn,
        ];
    }
}