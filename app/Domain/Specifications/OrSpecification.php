<?php
// Path: app/Domain/Specifications/OrSpecification.php

declare(strict_types=1);

namespace App\Domain\Specifications;

use App\Domain\Contracts\SpecificationInterface;

/**
 * Enterprise Domain: Or Specification
 * دمج قاعدتين يكفي أن تتحقق إحداهما (OR).
 */
class OrSpecification extends Specification
{
    protected SpecificationInterface $left;
    protected SpecificationInterface $right;

    public function __construct(SpecificationInterface $left, SpecificationInterface $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        return $this->left->isSatisfiedBy($candidate) || $this->right->isSatisfiedBy($candidate);
    }
}