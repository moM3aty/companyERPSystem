<?php
// Path: app/Modules/Accounting/Application/DTOs/CreateAccountDTO.php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs;

use InvalidArgumentException;

/**
 * Enterprise DTO: Create Account
 * ينقل بيانات إنشاء الحساب المحاسبي الجديد بأمان.
 */
class CreateAccountDTO
{
    public function __construct(
        public readonly int $companyId,
        public readonly string $accountCode,
        public readonly string $accountName,
        public readonly string $accountType,
        public readonly string $normalBalance,
        public readonly ?int $parentId = null,
        public readonly bool $isControlAccount = false,
        public readonly bool $isActive = true
    ) {
        if (empty(trim($this->accountCode))) {
            throw new InvalidArgumentException("Account code cannot be empty.");
        }
        
        if (empty(trim($this->accountName))) {
            throw new InvalidArgumentException("Account name cannot be empty.");
        }
        
        if (!in_array($this->accountType, ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'])) {
            throw new InvalidArgumentException("Invalid account type provided: {$this->accountType}");
        }
        
        if (!in_array($this->normalBalance, ['Debit', 'Credit'])) {
            throw new InvalidArgumentException("Normal balance must be Debit or Credit.");
        }
    }
}