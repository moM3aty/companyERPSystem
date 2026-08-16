<?php
// Path: app/Modules/Accounting/Domain/Repositories/JournalEntryRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Repositories;

interface JournalEntryRepositoryInterface
{
    public function getAll(int $companyId, array $filters = []): array;
    
    public function findById(int $id, int $companyId): ?array;
    
    /**
     * إنشاء القيد والأسطر معاً داخل Database Transaction
     */
    public function createWithLines(array $headerData, array $linesData, int $companyId, int $userId): int;
    
    /**
     * ترحيل القيد لدفتر الأستاذ
     */
    public function post(int $id, int $companyId, int $userId): bool;
    
    /**
     * إلغاء القيد (Void) لأغراض الرقابة بدلاً من الحذف
     */
    public function void(int $id, int $companyId, int $userId): bool;
}