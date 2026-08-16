<?php
// Path: app/Modules/Accounting/Domain/Entities/Account.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

use InvalidArgumentException;
use DomainException;

/**
 * Enterprise Domain Entity: Account (حساب الأستاذ العام)
 * يمثل حساباً منفرداً في شجرة الحسابات، ويحتوي على قواعد البزنس الخاصة به.
 */
class Account
{
    private ?int $id;
    private int $companyId;
    private string $accountCode;
    private string $accountName;
    private string $accountType; // Asset, Liability, Equity, Revenue, Expense
    private string $normalBalance; // Debit, Credit
    private ?int $parentId;
    private bool $isControlAccount;
    private bool $isActive;
    private float $currentBalance;

    public function __construct(
        int $companyId,
        string $accountCode,
        string $accountName,
        string $accountType,
        string $normalBalance,
        ?int $parentId = null,
        bool $isControlAccount = false,
        bool $isActive = true,
        float $currentBalance = 0.0,
        ?int $id = null
    ) {
        $this->companyId = $companyId;
        $this->setAccountCode($accountCode);
        $this->setAccountName($accountName);
        $this->setAccountType($accountType);
        $this->setNormalBalance($normalBalance);
        
        $this->parentId = $parentId;
        $this->isControlAccount = $isControlAccount;
        $this->isActive = $isActive;
        $this->currentBalance = $currentBalance;
        $this->id = $id;
    }

    
    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getAccountCode(): string { return $this->accountCode; }
    public function getAccountName(): string { return $this->accountName; }
    public function getAccountType(): string { return $this->accountType; }
    public function getNormalBalance(): string { return $this->normalBalance; }
    public function getParentId(): ?int { return $this->parentId; }
    public function isControlAccount(): bool { return $this->isControlAccount; }
    public function isActive(): bool { return $this->isActive; }
    public function getCurrentBalance(): float { return $this->currentBalance; }

    public function deactivate(): void
    {
        if ($this->currentBalance !== 0.0) {
            throw new DomainException("Cannot deactivate an account with a non-zero balance.");
        }
        $this->isActive = false;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    
    private function setAccountCode(string $code): void
    {
        if (empty(trim($code))) {
            throw new InvalidArgumentException("Account code cannot be empty.");
        }
        $this->accountCode = trim($code);
    }

    private function setAccountName(string $name): void
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException("Account name cannot be empty.");
        }
        $this->accountName = trim($name);
    }

    private function setAccountType(string $type): void
    {
        $validTypes = ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'];
        if (!in_array($type, $validTypes, true)) {
            throw new InvalidArgumentException("Invalid account type. Must be one of: " . implode(', ', $validTypes));
        }
        $this->accountType = $type;
    }

    private function setNormalBalance(string $balance): void
    {
        if (!in_array($balance, ['Debit', 'Credit'], true)) {
            throw new InvalidArgumentException("Normal balance must be either 'Debit' or 'Credit'.");
        }
        $this->normalBalance = $balance;
    }
}