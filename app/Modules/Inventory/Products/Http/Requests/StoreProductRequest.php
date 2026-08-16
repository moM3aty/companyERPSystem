<?php
// Path: app/Modules/Inventory/Products/Http/Requests/StoreProductRequest.php

declare(strict_types=1);

namespace App\Modules\Inventory\Products\Http\Requests;

use App\Core\Database\DatabaseManager;
use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Max;
use App\Core\Validation\Rules\In;
use App\Core\Validation\Rules\Boolean;
use App\Core\Validation\Rules\Unique;
use App\Core\Validation\Rules\Exists;

/**
 * Enterprise Request Validation: Store Product
 * يضمن صحة وتكامل بيانات الصنف (مراعاة طرق التقييم وأنواع التخزين).
 */
class StoreProductRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * التحقق من البيانات.
     *
     * @param array $data
     * @param int $companyId
     * @return array
     * @throws \App\Core\Exceptions\ValidationException
     */
    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'code'           => [new Required(), new StringRule(), new Max(50), new Unique($this->db, 'products', 'code', null, $companyId)],
            'barcode'        => [new StringRule(), new Max(50), new Unique($this->db, 'products', 'barcode', null, $companyId)],
            'name'           => [new Required(), new StringRule(), new Max(255)],
            'type'           => [new Required(), new In(['storable', 'service', 'consumable'])],
            'cost_method'    => [new Required(), new In(['fifo', 'average', 'standard'])],
            'base_unit_id'   => [new Required(), new Exists($this->db, 'units', 'id', $companyId)], // وحدة القياس الأساسية للصنف
            'category_id'    => [new Exists($this->db, 'product_categories', 'id', $companyId)],
            'brand_id'       => [new Exists($this->db, 'product_brands', 'id', $companyId)],
            'default_tax_id' => [new Exists($this->db, 'taxes', 'id', $companyId)],
            'is_active'      => [new Boolean()],
            'track_batches'  => [new Boolean()],
            'track_serials'  => [new Boolean()],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}