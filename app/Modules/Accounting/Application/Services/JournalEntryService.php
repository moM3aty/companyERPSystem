<?php
// Path: app/Modules/Accounting/Application/Services/JournalEntryService.php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Services;

use App\Modules\Accounting\Domain\Repositories\JournalEntryRepositoryInterface;
use App\Modules\Accounting\Core\JournalPostingService;
use App\Modules\Accounting\Application\DTOs\CreateJournalEntryDTO;
use Exception;

/**
 * Application Service: Journal Entry
 * المايسترو المسؤول عن القيود. يربط بين الـ Core Rules والـ Infrastructure.
 */
class JournalEntryService
{
    public function __construct(
        private readonly JournalEntryRepositoryInterface $journalRepository,
        private readonly JournalPostingService $postingService
    ) {}

    public function createAndPostEntry(CreateJournalEntryDTO $dto, bool $autoPost = false): int
    {
        // 1. تحويل الـ DTO للـ Array المتوقع في الـ Service/Repository
        $linesArray = [];
        foreach ($dto->lines as $line) {
            $linesArray[] = [
                'account_id' => $line->accountId,
                'cost_center_id' => $line->costCenterId,
                'debit' => $line->debit,
                'credit' => $line->credit,
                'description' => $line->description
            ];
        }

        // 2. التحقق الصارم من قواعد البزنس عبر الـ Core Service (متزن، فترة مفتوحة، الخ)
        $this->postingService->validateEntry($dto->companyId, $dto->entryDate, $linesArray);

        // 3. تجهيز الـ Header
        $headerData = [
            'entry_date' => $dto->entryDate,
            'description' => $dto->description,
            'reference_type' => $dto->referenceType,
            'reference_id' => $dto->referenceId,
            'currency_id' => $dto->currencyId,
            'status' => 'draft' // نجعله مسودة افتراضياً
        ];

        // 4. الحفظ في الـ Database ككتلة واحدة (Transaction داخل الـ Repo)
        $journalId = $this->journalRepository->createWithLines($headerData, $linesArray, $dto->companyId, $dto->userId);

        // 5. الترحيل التلقائي إذا طلب ذلك
        if ($autoPost) {
            $this->journalRepository->post($journalId, $dto->companyId, $dto->userId);
        }

        return $journalId;
    }
    
    public function voidEntry(int $journalId, int $companyId, int $userId): bool
    {
        $entry = $this->journalRepository->findById($journalId, $companyId);
        if (!$entry) {
            throw new Exception("Journal Entry not found.");
        }

        // استخدام الـ Core Service للتحقق مما إذا كان مسموحاً إلغاء القيد
        $this->postingService->canBeVoided($entry['status'], $entry['entry_date'], $companyId);

        return $this->journalRepository->void($journalId, $companyId, $userId);
    }
}