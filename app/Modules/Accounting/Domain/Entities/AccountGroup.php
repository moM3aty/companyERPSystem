<?php
// Path: app/Modules/Accounting/Domain/Entities/AccountGroup.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

use InvalidArgumentException;

/**
 * Enterprise Domain Entity: Account Group (مجموعة حسابات)
 * يمثل العقد الأبوية (Parent Nodes) في الدليل المحاسبي والتي لا يتم التوجيه عليها مباشرة (Non-postable).
 */
class AccountGroup
{
    private ?int $id;
    private int $companyId;
    private string $groupCode;
    private string $groupName;
    private ?int $parentId;
    private int $level;

    public function __construct(
        int $companyId,
        string $groupCode,
        string $groupName,
        int $level = 1,
        ?int $parentId = null,
        ?int $id = null
    ) {
        if (empty(trim($groupCode))) {
            throw new InvalidArgumentException("Group code cannot be empty.");
        }

        if (empty(trim($groupName))) {
            throw new InvalidArgumentException("Group name cannot be empty.");
        }

        if ($level < 1) {
            throw new InvalidArgumentException("Level must be 1 or greater.");
        }

        $this->companyId = $companyId;
        $this->groupCode = trim($groupCode);
        $this->groupName = trim($groupName);
        $this->level = $level;
        $this->parentId = $parentId;
        $this->id = $id;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getGroupCode(): string { return $this->groupCode; }
    public function getGroupName(): string { return $this->groupName; }
    public function getParentId(): ?int { return $this->parentId; }
    public function getLevel(): int { return $this->level; }

    public function rename(string $newName): void
    {
        if (empty(trim($newName))) {
            throw new InvalidArgumentException("New group name cannot be empty.");
        }
        $this->groupName = trim($newName);
    }
}