<?php
// Path: app/Modules/Accounting/Http/Requests/ReconciliationRequest.php

declare(strict_types=1);

namespace App\Modules\Accounting\Http\Requests;

use App\Core\Http\Request;
use App\Modules\Accounting\Application\DTOs\ReconciliationDTO;
use InvalidArgumentException;

class ReconciliationRequest
{
    public static function validateAndCreateDTO(Request $request, int $companyId): ReconciliationDTO
    {
        $bankAccountId = (int) $request->input('bank_account_id', 0);
        $statementDate = $request->input('statement_date');
        $endingBalance = (float) $request->input('ending_balance', 0.00);
        $matchedLines = $request->input('matched_lines', []);

        if ($bankAccountId <= 0 || !$statementDate) {
            throw new InvalidArgumentException("Bank Account ID and Statement Date are required.");
        }

        if (!is_array($matchedLines)) {
            throw new InvalidArgumentException("Matched lines must be an array.");
        }

        return new ReconciliationDTO(
            companyId: $companyId,
            bankAccountId: $bankAccountId,
            statementDate: $statementDate,
            statementEndingBalance: $endingBalance,
            matchedJournalLineIds: array_map('intval', $matchedLines)
        );
    }
}