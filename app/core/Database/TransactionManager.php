<?php
// Path: app/Core/Database/TransactionManager.php

declare(strict_types=1);

namespace App\Core\Database;

use Closure;
use Throwable;
use App\Core\Exceptions\DatabaseException;

/**
 * Enterprise Transaction Manager
 * ينفذ مجموعة من العمليات داخل سياق قاعدة البيانات بنظام الـ Transactions.
 * يضمن تكامل البيانات (ACID) ويدعم المحاولات التلقائية في حالات الـ Deadlocks.
 */
class TransactionManager
{
    protected DatabaseManager $db;

    /**
     * TransactionManager constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * تنفيذ مجموعة عمليات (Closure) داخل Transaction واحد.
     * إذا نجحت العمليات، يتم عمل Commit. وإذا فشلت، يتم عمل RollBack.
     *
     * @param Closure $callback الدالة التي تحتوي على العمليات
     * @param int $attempts عدد محاولات إعادة التنفيذ في حالة الـ Deadlock
     * @return mixed القيمة الراجعة من الـ Callback
     * @throws Throwable
     */
    public function execute(Closure $callback, int $attempts = 1): mixed
    {
        for ($currentAttempt = 1; $currentAttempt <= $attempts; $currentAttempt++) {
            
            $this->db->connection()->beginTransaction();

            try {
                $result = $callback($this->db->connection());

                $this->db->connection()->commit();

                return $result;

            } catch (Throwable $e) {
                $this->db->connection()->rollBack();

                if ($this->causedByDeadlock($e) && $currentAttempt < $attempts) {
                    // انتظار 50 ملي ثانية قبل إعادة المحاولة لمنع استمرار التعارض
                    usleep(50000);
                    continue;
                }

                // إذا لم يكن خطأ Deadlock، أو نفدت المحاولات، نرمي الخطأ للأعلى
                throw $e;
            }
        }

        return null;
    }

    /**
     * التحقق مما إذا كان الخطأ ناتج عن Deadlock أو Lock Timeout في قاعدة البيانات.
     * الأكواد 1213 و 1205 هي أكواد قياسية في MySQL.
     *
     * @param Throwable $e
     * @return bool
     */
    protected function causedByDeadlock(Throwable $e): bool
    {
        $message = $e->getMessage();

        return str_contains($message, 'Deadlock found when trying to get lock') ||
               str_contains($message, 'Lock wait timeout exceeded');
    }
}