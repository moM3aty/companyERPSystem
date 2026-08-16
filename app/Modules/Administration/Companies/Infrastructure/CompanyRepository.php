<?php
// Path: app/Modules/Administration/Companies/Infrastructure/CompanyRepository.php

declare(strict_types=1);

namespace App\Modules\Administration\Companies\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Administration\Companies\Domain\CompanyRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Company
 * هذا المستودع يدير الشركات نفسها، لذا يتم إيقاف (Tenant Scope) هنا لأنه يعمل على مستوى (Super Admin).
 */
class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    protected string $table = 'companies';
    protected bool $useTenantScope = false; // لا نستخدم Tenant Scope هنا أبداً
    protected bool $useSoftDeletes = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function registrationExists(string $registrationNumber): bool
    {
        $result = $this->newQuery()
            ->where('registration_number', '=', $registrationNumber)
            ->first();

        return $result !== null;
    }
}