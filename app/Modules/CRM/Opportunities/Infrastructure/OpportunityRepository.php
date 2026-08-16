<?php
// Path: app/Modules/CRM/Opportunities/Infrastructure/OpportunityRepository.php

declare(strict_types=1);

namespace App\Modules\CRM\Opportunities\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\CRM\Opportunities\Domain\OpportunityRepositoryInterface;

class OpportunityRepository extends BaseRepository implements OpportunityRepositoryInterface
{
    protected string $table = 'crm_opportunities';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function updateStage(int $opportunityId, string $stage, int $probability): void
    {
        $this->update($opportunityId, [
            'stage'       => $stage,
            'probability' => $probability,
            'updated_at'  => date('Y-m-d H:i:s')
        ]);
    }
}