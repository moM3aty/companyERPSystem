<?php
// Path: app/Modules/Inventory/Transfers/Http/Requests/StoreStockTransferRequest.php

declare(strict_types=1);

namespace App\Modules\Inventory\Transfers\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StoreStockTransferRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'from_warehouse_id' => [new Required(), new Exists($this->db, 'warehouses', 'id', $companyId)],
            'to_warehouse_id'   => [new Required(), new Exists($this->db, 'warehouses', 'id', $companyId)],
            'transfer_date'     => [new Required(), new Date('Y-m-d')],
            'items'             => [new Required()],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        // منطق أمان إضافي: لا يمكن التحويل لنفس المستودع!
        if ($validated['from_warehouse_id'] == $validated['to_warehouse_id']) {
            throw new ValidationException(['to_warehouse_id' => ['Source and destination warehouses cannot be the same.']]);
        }

        if (!is_array($data['items']) || count($data['items']) === 0) {
            throw new ValidationException(['items' => ['Transfer must contain at least one item.']]);
        }

        foreach ($data['items'] as $index => $item) {
            $itemRules = [
                'product_id' => [new Required(), new Exists($this->db, 'products', 'id', $companyId)],
                'quantity'   => [new Required(), new Numeric(), new Min(0.01)],
            ];
            
            try {
                $validatedItem = ValidatorFactory::makeAndValidate($item, $itemRules);
                $validated['items'][$index] = $validatedItem;
            } catch (ValidationException $e) {
                throw new ValidationException(["items.{$index}" => $e->getErrors()], "Validation failed for transfer item at row " . ($index + 1));
            }
        }

        return $validated;
    }
}