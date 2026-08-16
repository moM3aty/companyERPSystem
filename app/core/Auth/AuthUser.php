<?php
// Path: app/Core/Auth/AuthUser.php

declare(strict_types=1);

namespace App\Core\Auth;

use JsonSerializable;

/**
 * Enterprise Authenticated User DTO
 * Represents the currently logged-in user. Properties are readonly to prevent
 * runtime state tampering during the request lifecycle.
 */
class AuthUser implements JsonSerializable
{
    public readonly int $id;
    public readonly int $companyId;
    public readonly string $username;
    public readonly string $email;
    public readonly ?int $employeeId;
    public readonly string $language;
    public readonly string $timezone;

    /**
     * AuthUser constructor.
     *
     * @param int $id
     * @param int $companyId
     * @param string $username
     * @param string $email
     * @param int|null $employeeId
     * @param string $language
     * @param string $timezone
     */
    public function __construct(
        int $id,
        int $companyId,
        string $username,
        string $email,
        ?int $employeeId = null,
        string $language = 'ar',
        string $timezone = 'Asia/Riyadh'
    ) {
        $this->id = $id;
        $this->companyId = $companyId;
        $this->username = $username;
        $this->email = $email;
        $this->employeeId = $employeeId;
        $this->language = $language;
        $this->timezone = $timezone;
    }

    /**
     * Convert the user object to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->companyId,
            'username' => $this->username,
            'email' => $this->email,
            'employee_id' => $this->employeeId,
            'language' => $this->language,
            'timezone' => $this->timezone,
        ];
    }

    /**
     * JSON Serialization for API responses.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}