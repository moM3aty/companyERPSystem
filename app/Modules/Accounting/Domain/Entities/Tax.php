<?php
// Path: app/Modules/Accounting/Domain/Entities/Tax.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

use InvalidArgumentException;

/**
 * Enterprise Domain Entity: Tax (الضريبة)
 * يحتوي على معدلات الضرائب وارتباطها بحسابات الأستاذ العام للترحيل الآلي.
 */
class Tax
{
    private ?int $id;
    private int $companyId;
    private string $name;
    private float $ratePercentage;
    private string $scope; // 'sales', 'purchases', 'both'
    private ?int $salesAccountId;
    private ?int $purchaseAccountId;
    private bool $isActive;

    public function __construct(
        int $companyId,
        string $name,
        float $ratePercentage,
        string $scope = 'both',
        ?int $salesAccountId = null,
        ?int $purchaseAccountId = null,
        bool $isActive = true,
        ?int $id = null
    ) {
        if (empty(trim($name))) {
            throw new InvalidArgumentException("Tax name cannot be empty.");
        }

        if ($ratePercentage < 0.0) {
            throw new InvalidArgumentException("Tax rate cannot be negative.");
        }

        $validScopes = ['sales', 'purchases', 'both'];
        if (!in_array($scope, $validScopes, true)) {
            throw new InvalidArgumentException("Scope must be one of: " . implode(', ', $validScopes));
        }

        $this->companyId = $companyId;
        $this->name = trim($name);
        $this->ratePercentage = $ratePercentage;
        $this->scope = $scope;
        $this->salesAccountId = $salesAccountId;
        $this->purchaseAccountId = $purchaseAccountId;
        $this->isActive = $isActive;
        $this->id = $id;
    }

    /**
     * حساب قيمة الضريبة بناءً على المبلغ الأساسي (قبل الضريبة).
     */
    public function calculateTaxAmount(float $baseAmount): float
    {
        if ($baseAmount < 0) {
            return 0.0;
        }
        return round($baseAmount * ($this->ratePercentage / 100), 2);
    }

    /**
     * حساب إجمالي المبلغ متضمناً الضريبة.
     */
    public function calculateTotalAmount(float $baseAmount): float
    {
        return $baseAmount + $this->calculateTaxAmount($baseAmount);
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getName(): string { return $this->name; }
    public function getRatePercentage(): float { return $this->ratePercentage; }
    public function getScope(): string { return $this->scope; }
    public function getSalesAccountId(): ?int { return $this->salesAccountId; }
    public function getPurchaseAccountId(): ?int { return $this->purchaseAccountId; }
    public function isActive(): bool { return $this->isActive; }
}