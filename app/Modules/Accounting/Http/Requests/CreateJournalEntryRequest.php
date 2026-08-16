<?php
// Path: app/Modules/Accounting/Http/Requests/CreateJournalEntryRequest.php

declare(strict_types=1);

namespace App\Modules\Accounting\Http\Requests;

use App\Core\Http\Request;
use App\Modules\Accounting\Application\DTOs\CreateJournalEntryDTO;
use App\Modules\Accounting\Application\DTOs\JournalEntryLineDTO;
use InvalidArgumentException;

class CreateJournalEntryRequest
{
    public static function validateAndCreateDTO(Request $request, int $companyId, int $userId): CreateJournalEntryDTO
    {
        $entryDate = $request->input('entry_date');
        $description = $request->input('memo', '');
        $referenceType = $request->input('reference_type', 'Manual Entry');
        $referenceId = $request->input('reference_id') ? (int) $request->input('reference_id') : null;
        $currencyId = $request->input('currency_id') ? (int) $request->input('currency_id') : null;
        $linesData = $request->input('lines', []);

        if (!$entryDate) {
            throw new InvalidArgumentException("Entry date is required.");
        }

        if (!is_array($linesData) || count($linesData) < 2) {
            throw new InvalidArgumentException("A journal entry requires at least two lines.");
        }

        $lineDTOs = [];
        foreach ($linesData as $line) {
            $accountId = isset($line['account_id']) ? (int) $line['account_id'] : 0;
            if ($accountId <= 0) {
                throw new InvalidArgumentException("Invalid account ID provided in lines.");
            }

            $lineDTOs[] = new JournalEntryLineDTO(
                accountId: $accountId,
                debit: isset($line['debit']) ? (float) $line['debit'] : 0.00,
                credit: isset($line['credit']) ? (float) $line['credit'] : 0.00,
                description: $line['description'] ?? null,
                costCenterId: isset($line['cost_center_id']) ? (int) $line['cost_center_id'] : null
            );
        }

        return new CreateJournalEntryDTO(
            companyId: $companyId,
            userId: $userId,
            entryDate: $entryDate,
            description: $description,
            lines: $lineDTOs,
            referenceType: $referenceType,
            referenceId: $referenceId,
            currencyId: $currencyId
        );
    }
}