<?php
// Path: app/Modules/Payroll/Repositories/AllowanceRepository.php

declare(strict_types=1);

namespace App\Modules\Payroll\Repositories;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

class AllowanceRepository extends BaseRepository
{
    protected string $table = 'payroll_allowances';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }
}