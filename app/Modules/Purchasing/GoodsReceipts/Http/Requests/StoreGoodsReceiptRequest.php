<?php
// Path: app/Modules/Purchasing/GoodsReceipts/Http/Requests/StoreGoodsReceiptRequest.php

declare(strict_types=1);

namespace App\Modules\Purchasing\GoodsReceipts\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StoreGoodsReceiptRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'supplier_id'       => [new Required(), new Exists($this->db, 'suppliers', 'id', $companyId)],
            'purchase_order_id' => [new Exists($this->db, 'purchase_orders', 'id', $companyId)],
            'receipt_date'      => [new Required(), new Date('Y-m-d')],
            'reference_doc'     => [new StringRule()],
            'items'             => [new Required()], 
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!is_array($data['items']) || count($data['items']) === 0) {
            throw new ValidationException(['items' => ['GRN must contain at least one item.']]);
        }

        foreach ($data['items'] as $index => $item) {
            $itemRules = [
                'product_id'        => [new Required(), new Exists($this->db, 'products', 'id', $companyId)],
                'warehouse_id'      => [new Required(), new Exists($this->db, 'warehouses', 'id', $companyId)],
                'received_quantity' => [new Required(), new Numeric(), new Min(0.01)],
                'unit_cost'         => [new Numeric(), new Min(0)],
            ];
            
            try {
                $validatedItem = ValidatorFactory::makeAndValidate($item, $itemRules);
                $validated['items'][$index] = $validatedItem;
                $validated['items'][$index]['ordered_quantity'] = $item['ordered_quantity'] ?? 0.0;
            } catch (ValidationException $e) {
                throw new ValidationException(["items.{$index}" => $e->getErrors()], "Validation failed for GRN item at row " . ($index + 1));
            }
        }

        return $validated;
    }
}