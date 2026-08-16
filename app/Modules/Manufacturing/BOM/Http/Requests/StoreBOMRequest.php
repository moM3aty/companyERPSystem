<?php
// Path: app/Modules/Manufacturing/BOM/Http/Requests/StoreBOMRequest.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\BOM\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Unique;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

/**
 * Enterprise Request Validation: Store BOM
 * Ensures the BOM structure is valid before allowing it into the manufacturing engine.
 */
class StoreBOMRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'product_id'     => [new Required(), new Exists($this->db, 'products', 'id', $companyId)],
            'code'           => [new Required(), new StringRule(), new Unique($this->db, 'manufacturing_boms', 'code', null, $companyId)],
            'name'           => [new Required(), new StringRule()],
            'batch_quantity' => [new Required(), new Numeric(), new Min(0.01)], // Usually 1, but could be a batch of 100 for small items
            'items'          => [new Required()],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!is_array($data['items']) || count($data['items']) === 0) {
            throw new ValidationException(['items' => ['A Bill of Materials must contain at least one component.']]);
        }

        foreach ($data['items'] as $index => $item) {
            $itemRules = [
                'component_product_id' => [new Required(), new Exists($this->db, 'products', 'id', $companyId)],
                'quantity'             => [new Required(), new Numeric(), new Min(0.0001)],
                'scrap_percentage'     => [new Numeric(), new Min(0)],
                'unit_id'              => [new Exists($this->db, 'units', 'id', $companyId)],
            ];

            try {
                $validatedItem = ValidatorFactory::makeAndValidate($item, $itemRules);
                $validated['items'][$index] = $validatedItem;
                // Default scrap to 0 if not provided
                $validated['items'][$index]['scrap_percentage'] = $item['scrap_percentage'] ?? 0.00;
                $validated['items'][$index]['unit_id'] = $item['unit_id'] ?? null;
            } catch (ValidationException $e) {
                throw new ValidationException(["items.{$index}" => $e->getErrors()], "Validation failed for component at row " . ($index + 1));
            }
        }

        return $validated;
    }
}