<?php
// Path: app/Modules/POS/Shifts/Infrastructure/PosShiftRepository.php

declare(strict_types=1);

namespace App\Modules\POS\Shifts\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\POS\Shifts\Domain\PosShiftRepositoryInterface;

class PosShiftRepository extends BaseRepository implements PosShiftRepositoryInterface
{
    protected string $table = 'pos_shifts';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = false; // Shifts are financial boundaries, cannot be deleted

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function getActiveShiftForUser(int $userId, int $companyId): ?array
    {
        $result = $this->newQuery()
                       ->where('user_id', '=', $userId)
                       ->where('company_id', '=', $companyId)
                       ->where('status', '=', 'open')
                       ->first();

        return $result ?: null;
    }
}