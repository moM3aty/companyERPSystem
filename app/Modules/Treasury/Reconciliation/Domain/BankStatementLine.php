<?php
// Path: app/Modules/Treasury/Reconciliation/Domain/BankStatementLine.php

declare(strict_types=1);

namespace App\Modules\Treasury\Reconciliation\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Bank Statement Line
 * يمثل سطر حركة واحد داخل كشف الحساب البنكي المرفوع.
 */
class BankStatementLine extends Entity
{
    protected array $casts = [
        'id'                  => 'integer',
        'bank_statement_id'   => 'integer',
        'transaction_date'    => 'string',
        'description'         => 'string',
        'reference'           => 'string',
        'amount'              => 'float',   // موجب (إيداع) أو سالب (سحب)
        'is_matched'          => 'boolean', // هل تمت مطابقته مع حركة في النظام؟
        'matched_document_type'=> 'string', // 'receipt', 'payment_voucher', 'journal_entry'
        'matched_document_id' => 'integer',
    ];
}