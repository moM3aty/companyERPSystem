<?php
// Path: app/Modules/Accounting/Domain/Entities/BankAccount.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

use DomainException;
use InvalidArgumentException;

/**
 * Enterprise Domain Entity: Bank Account (الحساب البنكي / الخزينة)
 * يدير أرصدة السيولة بشكل منفصل عن شجرة الحسابات لحركات التسوية والدفع.
 */
class BankAccount
{
    private ?int $id;
    private int $companyId;
    private string $accountName;
    private string $accountType; // 'bank', 'cash'
    private string $currencyCode;
    private ?string $accountNumber;
    private ?string $iban;
    private int $glAccountId; // ربط مباشر بحساب الأستاذ العام
    private float $currentBalance;
    private bool $isActive;

    public function __construct(
        int $companyId,
        string $accountName,
        string $accountType,
        int $glAccountId,
        string $currencyCode = 'SAR',
        ?string $accountNumber = null,
        ?string $iban = null,
        float $currentBalance = 0.0,
        bool $isActive = true,
        ?int $id = null
    ) {
        if (empty(trim($accountName))) {
            throw new InvalidArgumentException("Bank/Cash account name cannot be empty.");
        }

        $validTypes = ['bank', 'cash'];
        if (!in_array($accountType, $validTypes, true)) {
            throw new InvalidArgumentException("Account type must be 'bank' or 'cash'.");
        }

        $this->companyId = $companyId;
        $this->accountName = trim($accountName);
        $this->accountType = $accountType;
        $this->glAccountId = $glAccountId;
        $this->currencyCode = strtoupper(trim($currencyCode));
        $this->accountNumber = $accountNumber ? trim($accountNumber) : null;
        $this->iban = $iban ? trim($iban) : null;
        $this->currentBalance = $currentBalance;
        $this->isActive = $isActive;
        $this->id = $id;
    }

    
    /**
     * إيداع مبلغ في الحساب.
     */
    public function deposit(float $amount): void
    {
        if (!$this->isActive) {
            throw new DomainException("Cannot deposit into an inactive account.");
        }
        
        if ($amount <= 0) {
            throw new InvalidArgumentException("Deposit amount must be greater than zero.");
        }
        
        $this->currentBalance += round($amount, 2);
    }

    /**
     * سحب مبلغ من الحساب.
     */
    public function withdraw(float $amount, bool $allowOverdraft = false): void
    {
        if (!$this->isActive) {
            throw new DomainException("Cannot withdraw from an inactive account.");
        }
        
        if ($amount <= 0) {
            throw new InvalidArgumentException("Withdrawal amount must be greater than zero.");
        }

        if (!$allowOverdraft && ($this->currentBalance - $amount < 0)) {
            throw new DomainException("Insufficient funds. Overdraft is not allowed on this account.");
        }
        
        $this->currentBalance -= round($amount, 2);
    }

    
    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getAccountName(): string { return $this->accountName; }
    public function getAccountType(): string { return $this->accountType; }
    public function getCurrencyCode(): string { return $this->currencyCode; }
    public function getAccountNumber(): ?string { return $this->accountNumber; }
    public function getIban(): ?string { return $this->iban; }
    public function getGlAccountId(): int { return $this->glAccountId; }
    public function getCurrentBalance(): float { return $this->currentBalance; }
    public function isActive(): bool { return $this->isActive; }
}