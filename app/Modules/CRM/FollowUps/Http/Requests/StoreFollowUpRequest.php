<?php
// Path: app/Modules/CRM/FollowUps/Http/Requests/StoreFollowUpRequest.php

declare(strict_types=1);

namespace App\Modules\CRM\FollowUps\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\In;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;

class StoreFollowUpRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'entity_type'  => [new Required(), new In(['lead', 'opportunity', 'customer'])],
            'entity_id'    => [new Required()], // يتم التحقق ديناميكياً
            'assigned_to'  => [new Required(), new Exists($this->db, 'users', 'id', $companyId)],
            'scheduled_at' => [new Required(), new Date('Y-m-d H:i:s')],
            'type'         => [new Required(), new In(['call', 'email', 'meeting'])],
            'notes'        => [new StringRule()],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        // Dynamic Entity Validation
        $table = match ($validated['entity_type']) {
            'lead'        => 'crm_leads',
            'opportunity' => 'crm_opportunities',
            'customer'    => 'customers',
        };

        $existsRule = new Exists($this->db, $table, 'id', $companyId);
        if (!$existsRule->passes('entity_id', $validated['entity_id'], $validated)) {
             throw new \App\Core\Exceptions\ValidationException(['entity_id' => ["The selected {$validated['entity_type']} does not exist."]]);
        }

        return $validated;
    }
}