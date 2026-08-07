<?php
// المسار: app/views/accounting/income_statement.php
$revenues = $data['revenues'] ?? [];
$expenses = $data['expenses'] ?? [];

$totalRevenues = 0;
foreach($revenues as $rev) $totalRevenues += $rev->balance;

$totalExpenses = 0;
foreach($expenses as $exp) $totalExpenses += $exp->balance;

$netIncome = $totalRevenues - $totalExpenses;
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-success"></i> قائمة الدخل (Income Statement)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">بيان الأرباح والخسائر للمنشأة</p>
    </div>
    <button onclick="window.print()" class="btn btn-dark">
        <i class="fas fa-print"></i> طباعة التقرير
    </button>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto; box-shadow: none;">
    <div class="card-body" style="padding: 40px;">
        
        <div class="text-center mb-4 pb-3" style="border-bottom: 2px solid var(--text-dark);">
            <h1 style="font-size: 28px; font-weight: 900; color: var(--text-dark); margin-bottom: 8px;">قائمة الدخل</h1>
            <p style="font-size: 15px; color: var(--text-muted); font-weight: 600; margin: 0;">للفترة المالية المنتهية في <?php echo date('Y-m-d'); ?></p>
        </div>

        <!-- الإيرادات -->
        <div class="mb-4">
            <h4 style="font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 16px; border-bottom: 1px solid var(--border); padding-bottom: 8px;">الإيرادات والمبيعات (Revenues)</h4>
            <?php if(!empty($revenues)): foreach($revenues as $rev): ?>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px dashed var(--border);">
                    <span class="fw-bold"><span class="text-muted font-monospace me-2">[<?php echo htmlspecialchars($rev->code); ?>]</span> <?php echo htmlspecialchars($rev->name); ?></span>
                    <span class="font-monospace fw-bold" style="direction: ltr;"><?php echo number_format($rev->balance, 2); ?></span>
                </div>
            <?php endforeach; else: ?>
                <div class="text-center text-muted p-3">لا توجد حسابات إيرادات مسجلة بأرصدة.</div>
            <?php endif; ?>
            <div class="d-flex justify-content-between align-items-center p-3 mt-3 rounded" style="background: #f1f5f9; font-weight: 800; font-size: 16px;">
                <span>إجمالي الإيرادات:</span>
                <span class="font-monospace" style="direction: ltr;"><?php echo number_format($totalRevenues, 2); ?> ر.س</span>
            </div>
        </div>

        <!-- المصروفات -->
        <div class="mb-4">
            <h4 style="font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 16px; border-bottom: 1px solid var(--border); padding-bottom: 8px;">المصروفات والتكاليف (Expenses)</h4>
            <?php if(!empty($expenses)): foreach($expenses as $exp): ?>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px dashed var(--border);">
                    <span class="fw-bold"><span class="text-muted font-monospace me-2">[<?php echo htmlspecialchars($exp->code); ?>]</span> <?php echo htmlspecialchars($exp->name); ?></span>
                    <span class="font-monospace fw-bold" style="direction: ltr;"><?php echo number_format($exp->balance, 2); ?></span>
                </div>
            <?php endforeach; else: ?>
                <div class="text-center text-muted p-3">لا توجد حسابات مصروفات مسجلة بأرصدة.</div>
            <?php endif; ?>
            <div class="d-flex justify-content-between align-items-center p-3 mt-3 rounded" style="background: #f1f5f9; font-weight: 800; font-size: 16px;">
                <span>إجمالي المصروفات:</span>
                <span class="font-monospace" style="direction: ltr;"><?php echo number_format($totalExpenses, 2); ?> ر.س</span>
            </div>
        </div>

        <!-- صافي الدخل -->
        <?php $netClass = $netIncome >= 0 ? 'var(--success-light)' : 'var(--danger-light)'; ?>
        <div class="d-flex justify-content-between align-items-center p-4 mt-4 rounded" style="background: var(--text-dark); color: #fff; font-size: 22px; font-weight: 900;">
            <span><?php echo $netIncome >= 0 ? 'صافي الربح (Net Profit)' : 'صافي الخسارة (Net Loss)'; ?>:</span>
            <span class="font-monospace" style="color: <?php echo $netClass; ?>; direction: ltr;">
                <?php echo number_format(abs($netIncome), 2); ?> ر.س
            </span>
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