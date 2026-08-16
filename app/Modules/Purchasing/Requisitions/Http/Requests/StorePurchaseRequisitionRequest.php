<?php
// Path: app/Modules/Purchasing/Requisitions/Http/Requests/StorePurchaseRequisitionRequest.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Requisitions\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StorePurchaseRequisitionRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'department_id' => [new Required(), new Exists($this->db, 'organization_nodes', 'id', $companyId)],
            'required_date' => [new Required(), new Date('Y-m-d')],
            'justification' => [new Required(), new StringRule()],
            'items'         => [new Required()],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!is_array($data['items']) || count($data['items']) === 0) {
            throw new ValidationException(['items' => ['Requisition must contain at least one item.']]);
        }

        foreach ($data['items'] as $index => $item) {
            $itemRules = [
                'product_id'           => [new Required(), new Exists($this->db, 'products', 'id', $companyId)],
                'quantity'             => [new Required(), new Numeric(), new Min(0.01)],
                'estimated_unit_price' => [new Numeric(), new Min(0)],
            ];
            
            try {
                $validatedItem = ValidatorFactory::makeAndValidate($item, $itemRules);
                $validated['items'][$index] = $validatedItem;
                $validated['items'][$index]['description'] = $item['description'] ?? null;
            } catch (ValidationException $e) {
                throw new ValidationException(["items.{$index}" => $e->getErrors()], "Validation failed for item at row " . ($index + 1));
            }
        }

        return $validated;
    }
}