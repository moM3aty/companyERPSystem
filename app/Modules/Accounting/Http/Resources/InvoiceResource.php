<?php
// Path: app/Modules/Accounting/Http/Resources/InvoiceResource.php

declare(strict_types=1);

namespace App\Modules\Accounting\Http\Resources;

class InvoiceResource
{
    public static function toArray(array|object $invoice): array
    {
        $data = is_object($invoice) ? (array) $invoice : $invoice;

        return [
            'id' => $data['id'] ?? null,
            'invoice_no' => $data['invoice_no'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'date' => $data['invoice_date'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'total' => (float) ($data['grand_total'] ?? 0.00),
            'paid' => (float) ($data['paid_amount'] ?? 0.00),
            'balance' => (float) ($data['remaining_amount'] ?? 0.00),
            'status' => $data['status'] ?? null,
        ];
    }

    public static function collection(array $invoices): array
    {
        return array_map(fn($inv) => self::toArray($inv), $invoices);
    }
}