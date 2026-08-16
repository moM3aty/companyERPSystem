<?php
// Path: app/Modules/Inventory/StockTaking/Http/Requests/StoreStockCountRequest.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockTaking\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StoreStockCountRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'warehouse_id' => [new Required(), new Exists($this->db, 'warehouses', 'id', $companyId)],
            'count_date'   => [new Required(), new Date('Y-m-d')],
            'items'        => [new Required()],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!is_array($data['items']) || count($data['items']) === 0) {
            throw new ValidationException(['items' => ['Physical count must contain at least one item.']]);
        }

        foreach ($data['items'] as $index => $item) {
            $itemRules = [
                'product_id'       => [new Required(), new Exists($this->db, 'products', 'id', $companyId)],
                'counted_quantity' => [new Required(), new Numeric()], // قد تكون صفراً
            ];
            
            try {
                $validatedItem = ValidatorFactory::makeAndValidate($item, $itemRules);
                $validated['items'][$index] = $validatedItem;
            } catch (ValidationException $e) {
                throw new ValidationException(["items.{$index}" => $e->getErrors()], "Validation failed at row " . ($index + 1));
            }
        }

        return $validated;
    }
}