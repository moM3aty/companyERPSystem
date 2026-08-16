<?php
// Path: app/Modules/POS/Terminals/Domain/PosTerminalRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\POS\Terminals\Domain;

use App\Core\Contracts\RepositoryInterface;

interface PosTerminalRepositoryInterface extends RepositoryInterface
{
    public function findByCode(string $code, int $companyId): ?array;
}