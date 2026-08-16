<?php
// Path: app/Compliance/DataRetention/Infrastructure/RetentionPolicyRepository.php

declare(strict_types=1);

namespace App\Compliance\DataRetention\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Compliance\DataRetention\Domain\RetentionPolicyRepositoryInterface;

class RetentionPolicyRepository extends BaseRepository implements RetentionPolicyRepositoryInterface
{
    protected string $table = 'compliance_retention_policies';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function getActivePolicies(): array
    {
        return $this->newQuery()
                    ->where('is_active', '=', 1)
                    ->get();
    }
}