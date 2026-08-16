<?php
// Path: app/Core/MasterData/CurrencyRepository.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Currency Repository
 * Manages operations for system currencies.
 * Does not use Tenant Scope as Currencies are typically global across the ERP.
 */
class CurrencyRepository extends BaseRepository
{
    protected string $table = 'currencies';
    protected bool $useTenantScope = false; // Currencies are global

    /**
     * CurrencyRepository constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * Get a currency by its 3-letter code (e.g., USD, SAR, EGP).
     *
     * @param string $code
     * @return array|null
     */
    public function findByCode(string $code): ?array
    {
        $result = $this->newQuery()
                       ->where('code', '=', strtoupper($code))
                       ->first();

        return $result ?: null;
    }

    /**
     * Get the default/base currency for the system or a specific tenant.
     * In this basic implementation, we assume a flag 'is_base' exists,
     * or it falls back to the first active currency.
     *
     * @return array|null
     */
    public function getBaseCurrency(): ?array
    {
        // Assuming your schema might have an 'is_base' column. If not, fetching the first active.
        $result = $this->newQuery()
                       ->where('is_active', '=', 1)
                       ->orderBy('id', 'asc')
                       ->first();
                       
        return $result ?: null;
    }
}