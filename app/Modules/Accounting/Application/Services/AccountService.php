<?php
// Path: app/Modules/Accounting/Application/Services/AccountService.php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Services;

use App\Modules\Accounting\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Accounting\Application\DTOs\CreateAccountDTO;
use Exception;

/**
 * Application Service: Account
 * يدير عمليات دليل الحسابات (إنشاء، تعديل، بناء الشجرة).
 */
class AccountService
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository
    ) {}

    public function createAccount(CreateAccountDTO $dto): int
    {
        // 1. التحقق من عدم تكرار كود الحساب
        $existing = $this->accountRepository->findByCode($dto->accountCode, $dto->companyId);
        if ($existing !== null) {
            throw new Exception("Account code {$dto->accountCode} already exists.");
        }

        // 2. تجهيز البيانات للحفظ
        $data = [
            'company_id' => $dto->companyId,
            'parent_id' => $dto->parentId,
            'account_code' => $dto->accountCode,
            'account_name' => $dto->accountName,
            'account_type' => $dto->accountType,
            'normal_balance' => $dto->normalBalance,
            'is_control_account' => $dto->isControlAccount ? 1 : 0,
            'is_active' => $dto->isActive ? 1 : 0,
            // تحديد المستوى في الشجرة (Level) بناءً على الأب
            'level' => $this->calculateLevel($dto->parentId, $dto->companyId)
        ];

        return $this->accountRepository->create($data, $dto->companyId);
    }

    public function getChartOfAccountsTree(int $companyId): array
    {
        $flatAccounts = $this->accountRepository->getAll($companyId);
        return $this->buildTree($flatAccounts);
    }

    private function buildTree(array $elements, $parentId = null): array
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['parent_id'] === $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                $element['children'] = $children ?: [];
                $branch[] = $element;
            }
        }
        return $branch;
    }

    private function calculateLevel(?int $parentId, int $companyId): int
    {
        if ($parentId === null) {
            return 1;
        }
        $parent = $this->accountRepository->findById($parentId, $companyId);
        return $parent ? ((int)$parent['level'] + 1) : 1;
    }
}