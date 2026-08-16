<?php
// Path: app/Modules/Purchasing/Returns/Http/Requests/StoreDebitNoteRequest.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Returns\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\StringRule;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StoreDebitNoteRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'supplier_id'         => [new Required(), new Exists($this->db, 'suppliers', 'id', $companyId)],
            'purchase_invoice_id' => [new Exists($this->db, 'purchase_invoices', 'id', $companyId)],
            'note_date'           => [new Required(), new Date('Y-m-d')],
            'currency_id'         => [new Required(), new Exists($this->db, 'currencies', 'id')],
            'reason'              => [new StringRule()],
            'items'               => [new Required()],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!is_array($data['items']) || count($data['items']) === 0) {
            throw new ValidationException(['items' => ['Debit note must contain at least one item to return.']]);
        }

        foreach ($data['items'] as $index => $item) {
            $itemRules = [
                'product_id'   => [new Required(), new Exists($this->db, 'products', 'id', $companyId)],
                'warehouse_id' => [new Required(), new Exists($this->db, 'warehouses', 'id', $companyId)], // مستودع السحب
                'quantity'     => [new Required(), new Numeric(), new Min(0.01)],
                'unit_price'   => [new Required(), new Numeric(), new Min(0)],
                'tax_amount'   => [new Numeric(), new Min(0)],
            ];
            
            try {
                $validatedItem = ValidatorFactory::makeAndValidate($item, $itemRules);
                $validated['items'][$index] = $validatedItem;
                $validated['items'][$index]['tax_amount'] = $item['tax_amount'] ?? 0.00;
            } catch (ValidationException $e) {
                throw new ValidationException(["items.{$index}" => $e->getErrors()], "Validation failed at row " . ($index + 1));
            }
        }

        return $validated;
    }
}