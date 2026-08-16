<?php
// Path: app/Modules/Treasury/Receipts/Infrastructure/ReceiptRepository.php

declare(strict_types=1);

namespace App\Modules\Treasury\Receipts\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Treasury\Receipts\Domain\ReceiptRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Receipt
 */
class ReceiptRepository extends BaseRepository implements ReceiptRepositoryInterface
{
    protected string $table = 'treasury_receipts';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = false; // Financial documents are voided, never deleted.

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function generateReceiptNumber(int $companyId): string
    {
        // For standard setup without SequenceManager locking. (In production with high concurrency, use Core/Numbering).
        $prefix = 'RC-' . date('ym') . '-';
        
        $lastRow = $this->newQuery()
            ->select(['receipt_no'])
            ->where('company_id', '=', $companyId)
            ->where('receipt_no', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastRow) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastRow['receipt_no']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }
}