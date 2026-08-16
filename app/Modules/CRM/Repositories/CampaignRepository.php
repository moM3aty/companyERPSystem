<?php
// Path: app/Modules/CRM/Repositories/CampaignRepository.php

declare(strict_types=1);

namespace App\Modules\CRM\Repositories;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

class CampaignRepository extends BaseRepository
{
    protected string $table = 'crm_campaigns';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }
}