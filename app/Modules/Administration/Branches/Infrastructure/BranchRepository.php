<?php
// Path: app/Modules/Administration/Branches/Infrastructure/BranchRepository.php

declare(strict_types=1);

namespace App\Modules\Administration\Branches\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Administration\Branches\Domain\BranchRepositoryInterface;

class BranchRepository extends BaseRepository implements BranchRepositoryInterface
{
    protected string $table = 'branches';
    protected bool $useTenantScope = true; // الفروع تابعة للشركات بالتأكيد
    protected bool $useSoftDeletes = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function codeExists(string $code, int $companyId): bool
    {
        $result = $this->newQuery()
            ->where('code', '=', $code)
            ->where('company_id', '=', $companyId)
            ->first();

        return $result !== null;
    }
}