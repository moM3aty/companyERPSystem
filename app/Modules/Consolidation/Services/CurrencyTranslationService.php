<?php
// File 8: app/Modules/Consolidation/Services/CurrencyTranslationService.php
declare(strict_types=1);

namespace App\Modules\Consolidation\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

class CurrencyTranslationService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;

    public function __construct(DatabaseManager $db, TransactionManager $transaction)
    {
        $this->db = $db;
        $this->transaction = $transaction;
    }

    /**
     * ترجمة ميزانية مراجعة لشركة تابعة (أجنبية) إلى عملة المجموعة، باستخدام معيار IAS 21.
     */
    public function translateSubsidiaryBalances(int $periodId, int $subsidiaryCompanyId, float $closingRate, float $averageRate, float $historicalRate): void
    {
        $this->transaction->execute(function () use ($periodId, $subsidiaryCompanyId, $closingRate, $averageRate, $historicalRate) {
            
            // 1. مسح الترجمات السابقة لهذه الشركة في هذه الفترة
            $this->db->connection()->delete(
                "DELETE FROM consolidation_translated_balances WHERE period_id = ? AND subsidiary_company_id = ?",
                [$periodId, $subsidiaryCompanyId]
            );

            // 2. جلب أرصدة החسابات للشركة التابعة بنهاية الفترة
            $balances = $this->db->connection()->select(
                "SELECT account_id, account_type, balance_local_currency 
                 FROM accounting_trial_balances 
                 WHERE company_id = ? AND period_id = ?",
                [$subsidiaryCompanyId, $periodId]
            );

            $translatedData = [];
            foreach ($balances as $b) {
                // تحديد سعر الصرف بناءً على طبيعة الحساب (IAS 21 Rules)
                $rate = $closingRate; // Default for Assets & Liabilities
                
                if (in_array($b['account_type'], ['revenue', 'expense'])) {
                    $rate = $averageRate; // P&L accounts use average rate
                } elseif (in_array($b['account_type'], ['equity', 'retained_earnings'])) {
                    $rate = $historicalRate; // Equity uses historical rate
                }

                $translatedBalance = (float)$b['balance_local_currency'] * $rate;

                $translatedData[] = [
                    'period_id'             => $periodId,
                    'subsidiary_company_id' => $subsidiaryCompanyId,
                    'account_id'            => $b['account_id'],
                    'original_balance'      => $b['balance_local_currency'],
                    'applied_rate'          => $rate,
                    'translated_balance'    => $translatedBalance
                ];
            }

            // إدخال الأرصدة المترجمة
            foreach ($translatedData as $data) {
                $this->db->connection()->insert(
                    "INSERT INTO consolidation_translated_balances (period_id, subsidiary_company_id, account_id, original_balance, applied_rate, translated_balance) 
                     VALUES (?, ?, ?, ?, ?, ?)",
                    array_values($data)
                );
            }
        });
    }
}