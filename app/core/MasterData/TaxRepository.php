<?php
// Path: app/Core/MasterData/TaxRepository.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Tax Repository
 * Manages tax definitions (e.g., VAT 15%, Withholding Tax).
 */
class TaxRepository extends BaseRepository
{
    protected string $table = 'taxes';
    protected bool $useTenantScope = true; // Taxes must be scoped to the company

    /**
     * TaxRepository constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * Get all active taxes for the current company.
     *
     * @return array
     */
    public function getActiveTaxes(): array
    {
        return $this->newQuery()
                    ->where('is_active', '=', 1)
                    ->get();
    }

    /**
     * Find a tax by its unique code within the company.
     *
     * @param string $code
     * @param int $companyId
     * @return array|null
     */
    public function findByCode(string $code, int $companyId): ?array
    {
        $result = $this->newQuery()
                       ->where('code', '=', strtoupper($code))
                       ->where('company_id', '=', $companyId)
                       ->first();

        return $result ?: null;
    }
}