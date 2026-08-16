<?php
// Path: app/Modules/Accounting/Domain/Entities/CostCenter.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

use InvalidArgumentException;

/**
 * Enterprise Domain Entity: Cost Center (مركز التكلفة)
 * يستخدم للتحليل المالي التفصيلي (Analytical Accounting) لتوزيع الإيرادات والمصروفات.
 */
class CostCenter
{
    private ?int $id;
    private int $companyId;
    private string $code;
    private string $name;
    private ?int $parentId;
    private bool $isActive;

    public function __construct(
        int $companyId,
        string $code,
        string $name,
        ?int $parentId = null,
        bool $isActive = true,
        ?int $id = null
    ) {
        if (empty(trim($code))) {
            throw new InvalidArgumentException("Cost center code cannot be empty.");
        }

        if (empty(trim($name))) {
            throw new InvalidArgumentException("Cost center name cannot be empty.");
        }

        $this->companyId = $companyId;
        $this->code = trim($code);
        $this->name = trim($name);
        $this->parentId = $parentId;
        $this->isActive = $isActive;
        $this->id = $id;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function rename(string $newName): void
    {
        if (empty(trim($newName))) {
            throw new InvalidArgumentException("New name cannot be empty.");
        }
        $this->name = trim($newName);
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getCode(): string { return $this->code; }
    public function getName(): string { return $this->name; }
    public function getParentId(): ?int { return $this->parentId; }
    public function isActive(): bool { return $this->isActive; }
}