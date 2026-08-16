<?php
// Path: app/Domain/Common/Address.php

declare(strict_types=1);

namespace App\Domain\Common;

use App\Domain\Contracts\ValueObjectInterface;
use App\Domain\Exceptions\BusinessRuleViolationException;
use JsonSerializable;

/**
 * Enterprise Value Object: Address
 * نموذج موحد للعناوين يستخدم للعملاء، الموردين، والفروع.
 */
class Address implements ValueObjectInterface, JsonSerializable
{
    public function __construct(
        public readonly string $street,
        public readonly string $city,
        public readonly string $state,
        public readonly string $zipCode,
        public readonly string $countryCode
    ) {
        if (strlen($countryCode) !== 2) {
            throw new BusinessRuleViolationException("Invalid Country Code [{$countryCode}]. Must be 2-letter ISO format.");
        }
    }

    public function getFullAddress(): string
    {
        $parts = array_filter([$this->street, $this->city, $this->state, $this->zipCode, $this->countryCode]);
        return implode(', ', $parts);
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->getFullAddress() === $other->getFullAddress();
    }

    public function jsonSerialize(): array
    {
        return [
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zipCode,
            'country_code' => $this->countryCode,
            'full_address' => $this->getFullAddress()
        ];
    }
}