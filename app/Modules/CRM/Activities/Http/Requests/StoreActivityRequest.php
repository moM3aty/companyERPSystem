<?php
// Path: app/Modules/CRM/Activities/Http/Requests/StoreActivityRequest.php

declare(strict_types=1);

namespace App\Modules\CRM\Activities\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\In;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Date;
use App\Core\Database\DatabaseManager;

class StoreActivityRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'type'            => [new Required(), new In(['call', 'email', 'meeting', 'task'])],
            'related_to_type' => [new Required(), new In(['lead', 'customer', 'opportunity'])],
            'related_to_id'   => [new Required()], // Validation handled dynamically below
            'subject'         => [new Required(), new StringRule()],
            'description'     => [new StringRule()],
            'scheduled_at'    => [new Required(), new Date('Y-m-d H:i:s')],
            'assigned_to'     => [new Exists($this->db, 'users', 'id', $companyId)],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        // Dynamic target validation based on polymorphic relation
        $targetTable = match ($validated['related_to_type']) {
            'lead'        => 'crm_leads',
            'customer'    => 'customers',
            'opportunity' => 'crm_opportunities',
        };

        $existsRule = new Exists($this->db, $targetTable, 'id', $companyId);
        if (!$existsRule->passes('related_to_id', $validated['related_to_id'], $validated)) {
             throw new \App\Core\Exceptions\ValidationException(['related_to_id' => ["The selected related record does not exist."]]);
        }

        return $validated;
    }
}