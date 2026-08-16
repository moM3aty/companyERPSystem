<?php
// Path: app/Modules/Treasury/Repositories/TransferRepository.php

declare(strict_types=1);

namespace App\Modules\Treasury\Repositories;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Repository: Transfer
 * يتعامل مع عمليات التحويل المالي الداخلي.
 */
class TransferRepository extends BaseRepository
{
    protected string $table = 'treasury_transfers';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * توليد رقم متسلسل لعملية التحويل.
     */
    public function generateTransferNumber(int $companyId): string
    {
        $prefix = 'TRF-' . date('Ym') . '-';
        
        $lastRecord = $this->newQuery()
            ->select(['transfer_no'])
            ->where('company_id', '=', $companyId)
            ->where('transfer_no', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastRecord) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastRecord['transfer_no']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }
}