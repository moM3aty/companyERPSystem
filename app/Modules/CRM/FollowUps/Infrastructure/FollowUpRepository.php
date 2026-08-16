<?php
// Path: app/Modules/CRM/FollowUps/Infrastructure/FollowUpRepository.php

declare(strict_types=1);

namespace App\Modules\CRM\FollowUps\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\CRM\FollowUps\Domain\FollowUpRepositoryInterface;

class FollowUpRepository extends BaseRepository implements FollowUpRepositoryInterface
{
    protected string $table = 'crm_follow_ups';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function getPendingForUser(int $userId, int $companyId, string $date): array
    {
        $endOfDay = $date . ' 23:59:59';

        // نجلب كل המتابعات المجدولة حتى نهاية اليوم الحالي
        return $this->newQuery()
                    ->where('assigned_to', '=', $userId)
                    ->where('company_id', '=', $companyId)
                    ->where('status', '=', 'pending')
                    ->where('scheduled_at', '<=', $endOfDay)
                    ->orderBy('scheduled_at', 'asc')
                    ->get();
    }
}