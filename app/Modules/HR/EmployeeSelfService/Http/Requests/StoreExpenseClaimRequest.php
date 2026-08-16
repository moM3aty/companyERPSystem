<?php
// Path: app/Modules/HR/EmployeeSelfService/Http/Requests/StoreExpenseClaimRequest.php

declare(strict_types=1);

namespace App\Modules\HR\EmployeeSelfService\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StoreExpenseClaimRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'claim_date'  => [new Required(), new Date('Y-m-d')],
            'currency_id' => [new Required(), new Exists($this->db, 'currencies', 'id')],
            'purpose'     => [new Required(), new StringRule()],
            'items'       => [new Required()],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!is_array($data['items']) || count($data['items']) === 0) {
            throw new ValidationException(['items' => ['Expense claim must contain at least one item.']]);
        }

        foreach ($data['items'] as $index => $item) {
            $itemRules = [
                'expense_type' => [new Required(), new StringRule()],
                'receipt_date' => [new Required(), new Date('Y-m-d')],
                'amount'       => [new Required(), new Numeric(), new Min(0.01)],
                'description'  => [new Required(), new StringRule()],
            ];
            
            try {
                $validatedItem = ValidatorFactory::makeAndValidate($item, $itemRules);
                $validated['items'][$index] = $validatedItem;
                $validated['items'][$index]['attachment_path'] = $item['attachment_path'] ?? null;
            } catch (ValidationException $e) {
                throw new ValidationException(["items.{$index}" => $e->getErrors()], "Validation failed for item at row " . ($index + 1));
            }
        }

        return $validated;
    }
}