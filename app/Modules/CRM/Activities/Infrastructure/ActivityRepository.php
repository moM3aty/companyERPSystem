<?php
// Path: app/Modules/CRM/Activities/Infrastructure/ActivityRepository.php

declare(strict_types=1);

namespace App\Modules\CRM\Activities\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\CRM\Activities\Domain\ActivityRepositoryInterface;

class ActivityRepository extends BaseRepository implements ActivityRepositoryInterface
{
    protected string $table = 'crm_activities';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function getPendingActivitiesForUser(int $userId, int $companyId): array
    {
        return $this->newQuery()
                    ->where('assigned_to', '=', $userId)
                    ->where('company_id', '=', $companyId)
                    ->where('status', '=', 'pending')
                    ->orderBy('scheduled_at', 'asc')
                    ->get();
    }
}