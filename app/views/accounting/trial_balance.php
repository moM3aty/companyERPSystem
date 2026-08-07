<?php
// المسار: app/views/accounting/trial_balance.php
$accounts = $data['accounts'] ?? [];
$totalDebit = 0;
$totalCredit = 0;
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-scale-unbalanced text-primary"></i> ميزان المراجعة (Trial Balance)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">أرصدة الحسابات ومراجعة التوازن المالي</p>
    </div>
    <button onclick="window.print()" class="btn btn-dark">
        <i class="fas fa-print"></i> طباعة الميزان
    </button>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto; box-shadow: none;">
    <div class="card-body" style="padding: 40px;">
        
        <div class="text-center mb-4 pb-3" style="border-bottom: 2px solid var(--text-dark);">
            <h1 style="font-size: 28px; font-weight: 900; color: var(--text-dark); margin-bottom: 8px;">ميزان المراجعة</h1>
            <p style="font-size: 15px; color: var(--text-muted); font-weight: 600; margin: 0;">الأرصدة الحالية كما في تاريخ <?php echo date('Y-m-d'); ?></p>
        </div>
        
        <table class="table" style="border: 1px solid var(--border);">
            <thead style="background: #f8fafc;">
                <tr>
                    <th style="border-bottom: 2px solid var(--text-dark);">رقم الحساب</th>
                    <th style="border-bottom: 2px solid var(--text-dark);">اسم الحساب</th>
                    <th class="text-left" style="border-bottom: 2px solid var(--text-dark);">رصيد مدين (Debit)</th>
                    <th class="text-left" style="border-bottom: 2px solid var(--text-dark);">رصيد دائن (Credit)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($accounts as $acc): 
                    $debit = 0; $credit = 0;
                    if (in_array($acc->type, ['asset', 'expense'])) {
                        if ($acc->balance >= 0) $debit = $acc->balance; else $credit = abs($acc->balance);
                    } else {
                        if ($acc->balance >= 0) $credit = $acc->balance; else $debit = abs($acc->balance);
                    }
                    $totalDebit += $debit;
                    $totalCredit += $credit;
                ?>
                <tr>
                    <td class="font-monospace text-muted fw-bold"><?php echo htmlspecialchars($acc->code); ?></td>
                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($acc->name); ?></td>
                    <td class="font-monospace fw-bold text-left" style="direction:ltr;"><?php echo $debit > 0 ? number_format($debit, 2) : '-'; ?></td>
                    <td class="font-monospace fw-bold text-left" style="direction:ltr;"><?php echo $credit > 0 ? number_format($credit, 2) : '-'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot style="background: #f8fafc; border-top: 2px solid var(--text-dark);">
                <tr>
                    <td colspan="2" class="fw-bold" style="font-size: 16px; padding: 20px;">الإجمالي الكلي:</td>
                    <td class="font-monospace fw-bold text-left" style="font-size: 18px; direction:ltr; padding: 20px; border-bottom: 4px double var(--text-dark);"><?php echo number_format($totalDebit, 2); ?></td>
                    <td class="font-monospace fw-bold text-left" style="font-size: 18px; direction:ltr; padding: 20px; border-bottom: 4px double var(--text-dark);"><?php echo number_format($totalCredit, 2); ?></td>
                </tr>
            </tfoot>
        </table>

        <?php $isBalanced = round($totalDebit, 2) === round($totalCredit, 2); ?>
        <div class="text-center mt-4 p-3 rounded fw-bold fs-5" style="background: <?php echo $isBalanced ? 'var(--success-light)' : 'var(--danger-light)'; ?>; color: <?php echo $isBalanced ? 'var(--success)' : 'var(--danger)'; ?>;">
            <?php echo $isBalanced ? '<i class="fas fa-check-double"></i> الميزان متطابق ومتزن' : '<i class="fas fa-triangle-exclamation"></i> تحذير: يوجد عدم تطابق في الميزان! يرجى مراجعة القيود.'; ?>
        </div>

    </div>
</div>

<style>
    @media print {
        body { background: #fff !important; }
        .d-print-none, .sidebar, .topbar { display: none !important; }
        .main-content { margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
    }
</style>