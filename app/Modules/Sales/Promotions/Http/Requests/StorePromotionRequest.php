<?php
// Path: app/Modules/Sales/Promotions/Http/Requests/StorePromotionRequest.php

declare(strict_types=1);

namespace App\Modules\Sales\Promotions\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Max;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StorePromotionRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'name'             => [new Required(), new StringRule()],
            'product_id'       => [new Exists($this->db, 'products', 'id', $companyId)], // Nullable = applies to all
            'discount_percent' => [new Numeric(), new Min(0), new Max(100)],
            'fixed_price'      => [new Numeric(), new Min(0)],
            'start_date'       => [new Date('Y-m-d H:i:s')],
            'end_date'         => [new Date('Y-m-d H:i:s')],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!empty($validated['end_date']) && !empty($validated['start_date']) && $validated['end_date'] < $validated['start_date']) {
            throw new ValidationException(['end_date' => ['Promotion end date cannot be before the start date.']]);
        }

        if (empty($validated['discount_percent']) && empty($validated['fixed_price'])) {
            throw new ValidationException(['discount_percent' => ['You must provide either a discount percentage or a fixed price.']]);
        }

        return $validated;
    }
}