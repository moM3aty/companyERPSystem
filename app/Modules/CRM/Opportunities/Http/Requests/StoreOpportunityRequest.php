<?php
// Path: app/Modules/CRM/Opportunities/Http/Requests/StoreOpportunityRequest.php

declare(strict_types=1);

namespace App\Modules\CRM\Opportunities\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Max;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\In;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StoreOpportunityRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'title'               => [new Required(), new StringRule()],
            'customer_id'         => [new Exists($this->db, 'customers', 'id', $companyId)],
            'lead_id'             => [new Exists($this->db, 'crm_leads', 'id', $companyId)],
            'expected_revenue'    => [new Required(), new Numeric(), new Min(0)],
            'probability'         => [new Required(), new Numeric(), new Min(0), new Max(100)],
            'stage'               => [new Required(), new In(['prospecting', 'qualification', 'proposal', 'negotiation', 'closed_won', 'closed_lost'])],
            'expected_close_date' => [new Required(), new Date('Y-m-d')],
            'assigned_to'         => [new Required(), new Exists($this->db, 'users', 'id', $companyId)],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (empty($validated['customer_id']) && empty($validated['lead_id'])) {
            throw new ValidationException(['customer_id' => ['An opportunity must be linked to either a Customer or a Lead.']]);
        }

        return $validated;
    }
}