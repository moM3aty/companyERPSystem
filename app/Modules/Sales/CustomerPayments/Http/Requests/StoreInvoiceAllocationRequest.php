<?php
// Path: app/Modules/Sales/CustomerPayments/Http/Requests/StoreInvoiceAllocationRequest.php

declare(strict_types=1);

namespace App\Modules\Sales\CustomerPayments\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StoreInvoiceAllocationRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'receipt_id'  => [new Required(), new Exists($this->db, 'treasury_receipts', 'id', $companyId)],
            'allocations' => [new Required()], // مصفوفة الفواتير والمبالغ
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!is_array($data['allocations']) || count($data['allocations']) === 0) {
            throw new ValidationException(['allocations' => ['You must provide at least one invoice allocation.']]);
        }

        foreach ($data['allocations'] as $index => $allocation) {
            $allocRules = [
                'sales_invoice_id' => [new Required(), new Exists($this->db, 'sales_invoices', 'id', $companyId)],
                'amount'           => [new Required(), new Numeric(), new Min(0.01)],
            ];
            
            try {
                $validatedAlloc = ValidatorFactory::makeAndValidate($allocation, $allocRules);
                $validated['allocations'][$index] = $validatedAlloc;
            } catch (ValidationException $e) {
                throw new ValidationException(["allocations.{$index}" => $e->getErrors()], "Validation failed for allocation at row " . ($index + 1));
            }
        }

        return $validated;
    }
}