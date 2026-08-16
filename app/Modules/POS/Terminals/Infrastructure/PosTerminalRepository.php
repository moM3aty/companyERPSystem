<?php
// Path: app/Modules/POS/Terminals/Infrastructure/PosTerminalRepository.php

declare(strict_types=1);

namespace App\Modules\POS\Terminals\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\POS\Terminals\Domain\PosTerminalRepositoryInterface;

class PosTerminalRepository extends BaseRepository implements PosTerminalRepositoryInterface
{
    protected string $table = 'pos_terminals';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function findByCode(string $code, int $companyId): ?array
    {
        $result = $this->newQuery()
                       ->where('code', '=', $code)
                       ->where('company_id', '=', $companyId)
                       ->first();

        return $result ?: null;
    }
}