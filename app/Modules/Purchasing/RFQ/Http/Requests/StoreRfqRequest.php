<?php
// Path: app/Modules/Purchasing/RFQ/Http/Requests/StoreRfqRequest.php

declare(strict_types=1);

namespace App\Modules\Purchasing\RFQ\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StoreRfqRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'title'         => [new Required(), new StringRule()],
            'deadline_date' => [new Required(), new Date('Y-m-d')],
            'delivery_date' => [new Date('Y-m-d')],
            'items'         => [new Required()],
            'supplier_ids'  => [new Required()], // مصفوفة الموردين المدعوين
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!is_array($data['items']) || count($data['items']) === 0) {
            throw new ValidationException(['items' => ['RFQ must contain at least one item.']]);
        }

        if (!is_array($data['supplier_ids']) || count($data['supplier_ids']) === 0) {
            throw new ValidationException(['supplier_ids' => ['You must invite at least one supplier to bid.']]);
        }

        // Validate items
        foreach ($data['items'] as $index => $item) {
            $itemRules = [
                'product_id' => [new Required(), new Exists($this->db, 'products', 'id', $companyId)],
                'quantity'   => [new Required(), new Numeric(), new Min(0.01)],
            ];
            try {
                $validatedItem = ValidatorFactory::makeAndValidate($item, $itemRules);
                $validated['items'][$index] = $validatedItem;
                $validated['items'][$index]['description'] = $item['description'] ?? null;
            } catch (ValidationException $e) {
                throw new ValidationException(["items.{$index}" => $e->getErrors()], "Validation failed at item row " . ($index + 1));
            }
        }

        // Validate suppliers (must exist)
        foreach ($data['supplier_ids'] as $supplierId) {
            $supCheck = $this->db->connection()->selectOne(
                "SELECT id FROM suppliers WHERE id = ? AND company_id = ? AND deleted_at IS NULL", 
                [$supplierId, $companyId]
            );
            if (!$supCheck) {
                throw new ValidationException(['supplier_ids' => ["Supplier ID {$supplierId} is invalid or inactive."]]);
            }
        }

        return $validated;
    }
}