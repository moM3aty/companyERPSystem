<?php
// Path: app/Core/MasterData/ExchangeRateRepository.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Exchange Rate Repository
 * Manages currency exchange rates. Highly critical for multi-currency transactions.
 */
class ExchangeRateRepository extends BaseRepository
{
    protected string $table = 'exchange_rates';
    protected bool $useTenantScope = true; // Exchange rates might be specific to a company's financial policy

    /**
     * ExchangeRateRepository constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * Get the most recent exchange rate for a specific currency pair on or before a given date.
     * This is crucial for historical transactions (e.g., viewing an invoice from 3 months ago).
     *
     * @param int $baseCurrencyId
     * @param int $targetCurrencyId
     * @param string $date (Format: YYYY-MM-DD)
     * @return float|null
     */
    public function getRateForDate(int $baseCurrencyId, int $targetCurrencyId, string $date): ?float
    {
        // If currencies are the same, rate is always 1
        if ($baseCurrencyId === $targetCurrencyId) {
            return 1.000000;
        }

        $query = $this->newQuery()
                      ->select(['rate'])
                      ->where('base_currency_id', '=', $baseCurrencyId)
                      ->where('target_currency_id', '=', $targetCurrencyId)
                      ->where('effective_date', '<=', $date)
                      ->orderBy('effective_date', 'desc');

        $result = $query->first();

        return $result ? (float) $result['rate'] : null;
    }

    /**
     * Add a new exchange rate for a specific date.
     *
     * @param int $companyId
     * @param int $baseCurrencyId
     * @param int $targetCurrencyId
     * @param float $rate
     * @param string $effectiveDate
     * @return int Insert ID
     */
    public function addRate(int $companyId, int $baseCurrencyId, int $targetCurrencyId, float $rate, string $effectiveDate): int
    {
        return $this->create([
            'company_id'         => $companyId,
            'base_currency_id'   => $baseCurrencyId,
            'target_currency_id' => $targetCurrencyId,
            'rate'               => $rate,
            'effective_date'     => $effectiveDate,
            'created_at'         => date('Y-m-d H:i:s')
        ]);
    }
}