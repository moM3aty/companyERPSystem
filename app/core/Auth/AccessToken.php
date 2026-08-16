<?php
// Path: app/Core/Auth/AccessToken.php

declare(strict_types=1);

namespace App\Core\Auth;

use JsonSerializable;

/**
 * Enterprise DTO: Access Token
 * كائن يحمل بيانات التوكن (JWT) لتمريره بشكل مهيكل للـ API Responses.
 */
class AccessToken implements JsonSerializable
{
    public readonly string $token;
    public readonly int $expiresIn;
    public readonly string $tokenType;

    public function __construct(string $token, int $expiresIn, string $tokenType = 'Bearer')
    {
        $this->token = $token;
        $this->expiresIn = $expiresIn;
        $this->tokenType = $tokenType;
    }

    public function jsonSerialize(): array
    {
        return [
            'access_token' => $this->token,
            'token_type'   => $this->tokenType,
            'expires_in'   => $this->expiresIn,
        ];
    }
}