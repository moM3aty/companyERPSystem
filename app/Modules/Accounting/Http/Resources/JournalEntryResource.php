<?php
// Path: app/Modules/Accounting/Http/Resources/JournalEntryResource.php

declare(strict_types=1);

namespace App\Modules\Accounting\Http\Resources;

class JournalEntryResource
{
    public static function toArray(array|object $entry): array
    {
        $data = is_object($entry) ? (array) $entry : $entry;

        $lines = [];
        if (isset($data['lines']) && is_array($data['lines'])) {
            foreach ($data['lines'] as $line) {
                $lineArray = is_object($line) ? (array) $line : $line;
                $lines[] = [
                    'account_id' => $lineArray['account_id'] ?? null,
                    'account_code' => $lineArray['account_code'] ?? null,
                    'debit' => (float) ($lineArray['debit'] ?? 0.00),
                    'credit' => (float) ($lineArray['credit'] ?? 0.00),
                    'description' => $lineArray['description'] ?? null,
                ];
            }
        }

        return [
            'id' => $data['id'] ?? null,
            'entry_no' => $data['entry_no'] ?? $data['reference'] ?? null,
            'date' => $data['entry_date'] ?? null,
            'description' => $data['description'] ?? $data['memo'] ?? null,
            'status' => $data['status'] ?? null,
            'total_amount' => (float) ($data['total_amount'] ?? 0.00),
            'lines' => $lines
        ];
    }

    public static function collection(array $entries): array
    {
        return array_map(fn($entry) => self::toArray($entry), $entries);
    }
}