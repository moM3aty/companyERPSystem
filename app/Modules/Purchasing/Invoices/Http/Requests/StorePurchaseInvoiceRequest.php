<?php
// Path: app/Modules/Purchasing/Invoices/Http/Requests/StorePurchaseInvoiceRequest.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Invoices\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StorePurchaseInvoiceRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'supplier_id'      => [new Required(), new Exists($this->db, 'suppliers', 'id', $companyId)],
            'supplier_bill_no' => [new Required(), new StringRule()],
            'invoice_date'     => [new Required(), new Date('Y-m-d')],
            'due_date'         => [new Required(), new Date('Y-m-d')],
            'currency_id'      => [new Required(), new Exists($this->db, 'currencies', 'id')],
            'items'            => [new Required()], 
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!is_array($data['items']) || count($data['items']) === 0) {
            throw new ValidationException(['items' => ['The purchase invoice must contain at least one item.']]);
        }

        foreach ($data['items'] as $index => $item) {
            $itemRules = [
                'product_id'      => [new Required(), new Exists($this->db, 'products', 'id', $companyId)],
                'quantity'        => [new Required(), new Numeric(), new Min(0.01)],
                'unit_price'      => [new Required(), new Numeric(), new Min(0)],
                'discount_amount' => [new Numeric(), new Min(0)],
                'tax_amount'      => [new Numeric(), new Min(0)],
            ];
            
            try {
                $validatedItem = ValidatorFactory::makeAndValidate($item, $itemRules);
                $validated['items'][$index] = $validatedItem;
                $validated['items'][$index]['description'] = $item['description'] ?? null;
                $validated['items'][$index]['warehouse_id'] = $item['warehouse_id'] ?? null;
            } catch (ValidationException $e) {
                throw new ValidationException(["items.{$index}" => $e->getErrors()], "Validation failed for bill item at row " . ($index + 1));
            }
        }

        return $validated;
    }
}