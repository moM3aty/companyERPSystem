<?php
// Path: app/Domain/Specifications/Specification.php

declare(strict_types=1);

namespace App\Domain\Specifications;

use App\Domain\Contracts\SpecificationInterface;

/**
 * Enterprise Domain: Base Specification
 * يوفر التطبيق الافتراضي لدمج المواصفات المعقدة (And, Or, Not).
 */
abstract class Specification implements SpecificationInterface
{
    /**
     * @inheritDoc
     */
    abstract public function isSatisfiedBy(mixed $candidate): bool;

    /**
     * @inheritDoc
     */
    public function and(SpecificationInterface $other): SpecificationInterface
    {
        return new AndSpecification($this, $other);
    }

    /**
     * @inheritDoc
     */
    public function or(SpecificationInterface $other): SpecificationInterface
    {
        return new OrSpecification($this, $other);
    }

    /**
     * @inheritDoc
     */
    public function not(): SpecificationInterface
    {
        return new NotSpecification($this);
    }
}