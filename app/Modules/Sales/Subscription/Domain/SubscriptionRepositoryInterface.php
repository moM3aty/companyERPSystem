<?php
// Path: app/Modules/Sales/Subscription/Domain/SubscriptionRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Sales\Subscription\Domain;

use App\Core\Contracts\RepositoryInterface;

interface SubscriptionRepositoryInterface extends RepositoryInterface
{
    public function getDueSubscriptions(string $date): array;
}