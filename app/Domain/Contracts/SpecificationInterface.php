<?php
// Path: app/Domain/Contracts/SpecificationInterface.php

declare(strict_types=1);

namespace App\Domain\Contracts;

/**
 * Enterprise Domain Contract: Specification Interface
 * يمثل نمط المواصفات (Specification Pattern) الأساسي في ה-DDD.
 * يستخدم لتغليف القواعد التجارية (Business Rules) المعقدة لتكون قابلة لإعادة الاستخدام والدمج.
 */
interface SpecificationInterface
{
    /**
     * التحقق مما إذا كان الكائن (Candidate) يحقق شروط المواصفة.
     *
     * @param mixed $candidate
     * @return bool
     */
    public function isSatisfiedBy(mixed $candidate): bool;

    /**
     * دمج مواصفتين باستخدام بوابة AND المنطقية.
     *
     * @param SpecificationInterface $other
     * @return SpecificationInterface
     */
    public function and(SpecificationInterface $other): SpecificationInterface;

    /**
     * دمج مواصفتين باستخدام بوابة OR المنطقية.
     *
     * @param SpecificationInterface $other
     * @return SpecificationInterface
     */
    public function or(SpecificationInterface $other): SpecificationInterface;

    /**
     * نفي المواصفة (NOT).
     *
     * @return SpecificationInterface
     */
    public function not(): SpecificationInterface;
}