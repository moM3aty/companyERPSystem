<?php
// Path: app/Modules/Sales/Subscription/Infrastructure/SubscriptionRepository.php

declare(strict_types=1);

namespace App\Modules\Sales\Subscription\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Sales\Subscription\Domain\SubscriptionRepositoryInterface;

class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{
    protected string $table = 'sales_subscriptions';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function getDueSubscriptions(string $date): array
    {
        return $this->newQuery()
                    ->where('next_billing_date', '<=', $date)
                    ->where('status', '=', 'active')
                    ->get();
    }
}