<?php
// Path: app/Modules/CRM/Leads/Infrastructure/LeadRepository.php

declare(strict_types=1);

namespace App\Modules\CRM\Leads\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\CRM\Leads\Domain\LeadRepositoryInterface;

class LeadRepository extends BaseRepository implements LeadRepositoryInterface
{
    protected string $table = 'crm_leads';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function updateStatus(int $leadId, string $status): void
    {
        $this->update($leadId, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}