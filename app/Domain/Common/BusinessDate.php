<?php
// Path: app/Domain/Common/BusinessDate.php

declare(strict_types=1);

namespace App\Domain\Common;

use App\Domain\Contracts\ValueObjectInterface;
use App\Domain\Exceptions\BusinessRuleViolationException;
use DateTimeImmutable;
use Exception;
use JsonSerializable;

/**
 * Enterprise Value Object: BusinessDate
 * يغلف التعامل مع التواريخ لضمان عدم تغييرها عن طريق الخطأ (Immutable) وتوحيد صيغتها المحاسبية.
 */
class BusinessDate implements ValueObjectInterface, JsonSerializable
{
    protected DateTimeImmutable $date;

    public function __construct(string $dateString = 'now')
    {
        try {
            $this->date = new DateTimeImmutable($dateString);
        } catch (Exception $e) {
            throw new BusinessRuleViolationException("Invalid date format provided: {$dateString}");
        }
    }

    public function getNative(): DateTimeImmutable
    {
        return $this->date;
    }

    public function formatDb(): string
    {
        return $this->date->format('Y-m-d H:i:s');
    }

    public function formatShort(): string
    {
        return $this->date->format('Y-m-d');
    }

    public function isBefore(self $other): bool
    {
        return $this->date < $other->getNative();
    }

    public function isAfter(self $other): bool
    {
        return $this->date > $other->getNative();
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->formatDb() === $other->formatDb();
    }

    public function jsonSerialize(): string
    {
        return $this->formatDb();
    }
}