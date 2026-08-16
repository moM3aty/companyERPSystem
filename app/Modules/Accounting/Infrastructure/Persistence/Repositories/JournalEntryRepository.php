<?php
// Path: app/Modules/Accounting/Infrastructure/Persistence/Repositories/JournalEntryRepository.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence\Repositories;

use App\Modules\Accounting\Domain\Repositories\JournalEntryRepositoryInterface;
use App\Modules\Accounting\Infrastructure\Persistence\Models\JournalEntryModel;
use App\Modules\Accounting\Infrastructure\Persistence\Models\JournalEntryLineModel;
use App\Core\Database\DatabaseManager;
use Exception;

class JournalEntryRepository implements JournalEntryRepositoryInterface
{
    private JournalEntryModel $headerModel;
    private JournalEntryLineModel $lineModel;

    public function __construct()
    {
        $this->headerModel = new JournalEntryModel();
        $this->lineModel = new JournalEntryLineModel();
    }

    public function getAll(int $companyId, array $filters = []): array
    {
        return $this->headerModel->fetchAllWithTotals($companyId);
    }

    public function findById(int $id, int $companyId): ?array
    {
        // Omitted for brevity, fetch header and lines
        return null;
    }

    public function createWithLines(array $headerData, array $linesData, int $companyId, int $userId): int
    {
        $db = DatabaseManager::getConnection();
        
        try {
            $db->beginTransaction();

            $headerData['company_id'] = $companyId;
            $headerData['created_by'] = $userId;
            $headerData['entry_no'] = $headerData['entry_no'] ?? 'JE-' . time(); // Auto-generate if empty

            $journalId = $this->headerModel->insertHeader($headerData);

            foreach ($linesData as $line) {
                $line['journal_entry_id'] = $journalId;
                $this->lineModel->insertLine($line);
            }

            $db->commit();
            return $journalId;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function post(int $id, int $companyId, int $userId): bool
    {
        return $this->headerModel->updateStatus($id, $companyId, 'posted', $userId);
    }

    public function void(int $id, int $companyId, int $userId): bool
    {
        return $this->headerModel->updateStatus($id, $companyId, 'voided', $userId);
    }
}