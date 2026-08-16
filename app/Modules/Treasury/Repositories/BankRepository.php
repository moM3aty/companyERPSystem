<?php
// Path: app/Modules/Treasury/Repositories/BankRepository.php

declare(strict_types=1);

namespace App\Modules\Treasury\Repositories;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

class BankRepository extends BaseRepository
{
    protected string $table = 'treasury_banks';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }
}