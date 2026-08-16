<?php
// Path: app/Domain/Specifications/NotSpecification.php

declare(strict_types=1);

namespace App\Domain\Specifications;

use App\Domain\Contracts\SpecificationInterface;

/**
 * Enterprise Domain: Not Specification
 * نفي لقاعدة معينة (NOT).
 */
class NotSpecification extends Specification
{
    protected SpecificationInterface $specification;

    public function __construct(SpecificationInterface $specification)
    {
        $this->specification = $specification;
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        return !$this->specification->isSatisfiedBy($candidate);
    }
}