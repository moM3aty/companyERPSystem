<?php
// Path: app/Modules/Purchasing/PurchaseOrders/Http/Requests/StorePurchaseOrderRequest.php

declare(strict_types=1);

namespace App\Modules\Purchasing\PurchaseOrders\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

/**
 * Enterprise Request Validation: Store Purchase Order
 * يضمن صحة هيكل أمر الشراء والأصناف بداخلها قبل تمريرها للمحرك الرياضي.
 */
class StorePurchaseOrderRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        // 1. Header Validation
        $rules = [
            'supplier_id'            => [new Required(), new Exists($this->db, 'suppliers', 'id', $companyId)],
            'order_date'             => [new Required(), new Date('Y-m-d')],
            'expected_delivery_date' => [new Date('Y-m-d')],
            'currency_id'            => [new Required(), new Exists($this->db, 'currencies', 'id')],
            'items'                  => [new Required()], 
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        // 2. Line Items Validation
        if (!is_array($data['items']) || count($data['items']) === 0) {
            throw new ValidationException(['items' => ['The purchase order must contain at least one item.']]);
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
            } catch (ValidationException $e) {
                throw new ValidationException(["items.{$index}" => $e->getErrors()], "Validation failed for PO item at row " . ($index + 1));
            }
        }

        return $validated;
    }
}