<?php
// Path: app/Modules/Projects/Projects/Infrastructure/ProjectRepository.php

declare(strict_types=1);

namespace App\Modules\Projects\Projects\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Projects\Projects\Domain\ProjectRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Project
 */
class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    protected string $table = 'projects';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function generateProjectCode(int $companyId): string
    {
        $prefix = 'PRJ-' . date('Y') . '-';
        
        $lastProject = $this->newQuery()
            ->select(['code'])
            ->where('company_id', '=', $companyId)
            ->where('code', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastProject) {
            return $prefix . '001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastProject['code']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 3, '0', STR_PAD_LEFT);
    }
}