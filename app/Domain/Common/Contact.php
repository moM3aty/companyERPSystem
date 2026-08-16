<?php
// Path: app/Domain/Common/Contact.php

declare(strict_types=1);

namespace App\Domain\Common;

use App\Domain\Contracts\ValueObjectInterface;
use App\Domain\Exceptions\BusinessRuleViolationException;
use JsonSerializable;

/**
 * Enterprise Value Object: Contact
 * يغلف بيانات الاتصال ويقوم بتنظيفها وفحصها (Sanitization).
 */
class Contact implements ValueObjectInterface, JsonSerializable
{
    public readonly string $name;
    public readonly ?string $email;
    public readonly ?string $phone;

    public function __construct(string $name, ?string $email = null, ?string $phone = null)
    {
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BusinessRuleViolationException("Invalid email format: {$email}");
        }

        $cleanPhone = $phone ? preg_replace('/[^0-9+]/', '', $phone) : null;

        $this->name = trim($name);
        $this->email = $email ? strtolower(trim($email)) : null;
        $this->phone = $cleanPhone;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self 
            && $this->name === $other->name 
            && $this->email === $other->email 
            && $this->phone === $other->phone;
    }

    public function jsonSerialize(): array
    {
        return ['name' => $this->name, 'email' => $this->email, 'phone' => $this->phone];
    }
}