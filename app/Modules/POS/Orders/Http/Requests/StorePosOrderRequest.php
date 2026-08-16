<?php
// Path: app/Modules/POS/Orders/Http/Requests/StorePosOrderRequest.php

declare(strict_types=1);

namespace App\Modules\POS\Orders\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\In;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

/**
 * Enterprise Request Validation: Store POS Order
 * يضمن صحة إدخالات الكاشير وسرعتها قبل الحساب المالي الدقيق.
 */
class StorePosOrderRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'payment_method' => [new Required(), new In(['cash', 'card'])],
            'customer_id'    => [new Exists($this->db, 'customers', 'id', $companyId)], // Optional
            'items'          => [new Required()],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!is_array($data['items']) || count($data['items']) === 0) {
            throw new ValidationException(['items' => ['A POS order must contain at least one item.']]);
        }

        foreach ($data['items'] as $index => $item) {
            $itemRules = [
                'product_id'      => [new Required(), new Exists($this->db, 'products', 'id', $companyId)],
                'quantity'        => [new Required(), new Numeric(), new Min(0.01)],
                'unit_price'      => [new Required(), new Numeric(), new Min(0)],
                'discount_amount' => [new Numeric(), new Min(0)],
            ];
            
            try {
                $validatedItem = ValidatorFactory::makeAndValidate($item, $itemRules);
                $validated['items'][$index] = $validatedItem;
                // Default discount to 0 if not provided
                $validated['items'][$index]['discount_amount'] = $item['discount_amount'] ?? 0.00;
            } catch (ValidationException $e) {
                throw new ValidationException(["items.{$index}" => $e->getErrors()], "Validation failed for item at row " . ($index + 1));
            }
        }

        return $validated;
    }
}