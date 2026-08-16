<?php
// Path: app/Modules/Sales/Deliveries/Http/Requests/StoreDeliveryNoteRequest.php

declare(strict_types=1);

namespace App\Modules\Sales\Deliveries\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StoreDeliveryNoteRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'customer_id'    => [new Required(), new Exists($this->db, 'customers', 'id', $companyId)],
            'sales_order_id' => [new Exists($this->db, 'sales_orders', 'id', $companyId)],
            'delivery_date'  => [new Required(), new Date('Y-m-d')],
            'items'          => [new Required()], 
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!is_array($data['items']) || count($data['items']) === 0) {
            throw new ValidationException(['items' => ['Delivery Note must contain at least one item.']]);
        }

        foreach ($data['items'] as $index => $item) {
            $itemRules = [
                'product_id'         => [new Required(), new Exists($this->db, 'products', 'id', $companyId)],
                'warehouse_id'       => [new Required(), new Exists($this->db, 'warehouses', 'id', $companyId)],
                'delivered_quantity' => [new Required(), new Numeric(), new Min(0.01)],
            ];
            
            try {
                $validatedItem = ValidatorFactory::makeAndValidate($item, $itemRules);
                $validated['items'][$index] = $validatedItem;
                $validated['items'][$index]['ordered_quantity'] = $item['ordered_quantity'] ?? 0.0;
            } catch (ValidationException $e) {
                throw new ValidationException(["items.{$index}" => $e->getErrors()], "Validation failed for Delivery item at row " . ($index + 1));
            }
        }

        return $validated;
    }
}