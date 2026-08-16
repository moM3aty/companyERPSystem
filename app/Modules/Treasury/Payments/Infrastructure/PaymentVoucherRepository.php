<?php
// Path: app/Modules/Treasury/Payments/Infrastructure/PaymentVoucherRepository.php

declare(strict_types=1);

namespace App\Modules\Treasury\Payments\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Treasury\Payments\Domain\PaymentVoucherRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Payment Voucher
 */
class PaymentVoucherRepository extends BaseRepository implements PaymentVoucherRepositoryInterface
{
    protected string $table = 'treasury_payment_vouchers';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = false; // Financial documents are voided, never deleted.

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function generateVoucherNumber(int $companyId): string
    {
        $prefix = 'PV-' . date('ym') . '-';
        
        $lastRow = $this->newQuery()
            ->select(['voucher_no'])
            ->where('company_id', '=', $companyId)
            ->where('voucher_no', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastRow) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastRow['voucher_no']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }
}