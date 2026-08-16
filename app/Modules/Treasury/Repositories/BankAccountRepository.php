<?php
// Path: app/Modules/Treasury/Repositories/BankAccountRepository.php

declare(strict_types=1);

namespace App\Modules\Treasury\Repositories;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

class BankAccountRepository extends BaseRepository
{
    protected string $table = 'treasury_bank_accounts';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * التحقق من عدم تكرار رقم הـ IBAN داخل الشركة لتجنب المشاكل المحاسبية.
     */
    public function ibanExists(string $iban, int $companyId): bool
    {
        $result = $this->newQuery()
                       ->where('iban', '=', $iban)
                       ->where('company_id', '=', $companyId)
                       ->first();

        return $result !== null;
    }
}