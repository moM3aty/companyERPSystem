<?php
// app/views/reports/balance_sheet.php
$assets = $data['assets'] ?? null;
$liabilities = $data['liabilities'] ?? null;
$equity = $data['equity'] ?? null;
$asOfDate = $data['as_of_date'] ?? null;

$totalAssets = 0;
foreach($assets as $a) $totalAssets += $a->balance;

$totalLiabilities = 0;
foreach($liabilities as $l) $totalLiabilities += $l->balance;

$totalEquity = 0;
foreach($equity as $e) $totalEquity += $e->balance;

$totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-scale-balanced text-warning"></i> الميزانية العمومية (Balance Sheet)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">بيان المركز المالي للشركة بناءً على أرصدة الحسابات.</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-dark"><i class="fas fa-print"></i> طباعة التقرير</button>
        <a href="<?php echo URLROOT; ?>/report/index" class="btn btn-secondary">رجوع</a>
    </div>
</div>

<div class="card mb-4 d-print-none">
    <div class="card-body bg-light">
        <form action="<?php echo URLROOT; ?>/report/balanceSheet" method="GET" class="d-flex gap-3 align-items-end flex-wrap">
            <div style="flex: 1; max-width: 300px;">
                <label class="form-label">كما في تاريخ (As of Date)</label>
                <input type="date" name="as_of_date" class="form-control" value="<?php echo htmlspecialchars($asOfDate); ?>" required>
            </div>
            <div>
                <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> تحديث الميزانية</button>
            </div>
        </form>
    </div>
</div>

<div class="card" style="max-width: 1000px; margin: 0 auto; box-shadow: var(--shadow-md);">
    <div class="card-header bg-white text-center border-bottom pb-4 pt-4">
        <h2 class="fw-black text-dark mb-1">الميزانية العمومية (Balance Sheet)</h2>
        <h5 class="text-muted font-monospace mb-0">كما في: <?php echo $asOfDate; ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="row m-0">
            <!-- الأصول (Assets) -->
            <div class="col-md-6 p-0 border-end">
                <table class="table table-borderless mb-0 h-100">
                    <thead class="bg-success-light border-bottom">
                        <tr><th colspan="2"><h5 class="fw-bold text-success-dark mb-0">الأصول (Assets)</h5></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($assets as $a): ?>
                        <tr>
                            <td class="text-dark"><?php echo htmlspecialchars($a->account_code . ' - ' . $a->account_name); ?></td>
                            <td class="text-left font-monospace" style="direction:ltr;"><?php echo number_format($a->balance, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($assets)): ?><tr><td colspan="2" class="text-muted text-center py-3">لا توجد أرصدة أصول مسجلة.</td></tr><?php endif; ?>
                    </tbody>
                    <tfoot class="border-top border-2 border-success bg-light mt-auto">
                        <tr>
                            <td class="fw-bold text-dark py-3 fs-5">إجمالي الأصول:</td>
                            <td class="text-left font-monospace fw-bold text-success-dark py-3 fs-5" style="direction:ltr;"><?php echo number_format($totalAssets, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- الخصوم وحقوق الملكية -->
            <div class="col-md-6 p-0 d-flex flex-column">
                <table class="table table-borderless mb-0">
                    <thead class="bg-danger-light border-bottom">
                        <tr><th colspan="2"><h5 class="fw-bold text-danger-dark mb-0">الخصوم والالتزامات (Liabilities)</h5></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($liabilities as $l): ?>
                        <tr>
                            <td class="text-dark"><?php echo htmlspecialchars($l->account_code . ' - ' . $l->account_name); ?></td>
                            <td class="text-left font-monospace" style="direction:ltr;"><?php echo number_format($l->balance, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($liabilities)): ?><tr><td colspan="2" class="text-muted text-center py-3">لا توجد أرصدة خصوم مسجلة.</td></tr><?php endif; ?>
                    </tbody>
                    <tfoot class="border-top bg-light">
                        <tr>
                            <td class="fw-bold text-muted py-2">إجمالي الخصوم:</td>
                            <td class="text-left font-monospace fw-bold text-danger-dark py-2" style="direction:ltr;"><?php echo number_format($totalLiabilities, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>

                <table class="table table-borderless mb-0 mt-auto border-top">
                    <thead class="bg-info-light border-bottom">
                        <tr><th colspan="2"><h5 class="fw-bold text-info-dark mb-0">حقوق الملكية (Equity)</h5></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($equity as $e): ?>
                        <tr>
                            <td class="text-dark"><?php echo htmlspecialchars($e->account_code . ' - ' . $e->account_name); ?></td>
                            <td class="text-left font-monospace" style="direction:ltr;"><?php echo number_format($e->balance, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($equity)): ?><tr><td colspan="2" class="text-muted text-center py-3">لا توجد أرصدة حقوق ملكية مسجلة.</td></tr><?php endif; ?>
                    </tbody>
                    <tfoot class="border-top bg-light">
                        <tr>
                            <td class="fw-bold text-muted py-2">إجمالي حقوق الملكية:</td>
                            <td class="text-left font-monospace fw-bold text-info-dark py-2" style="direction:ltr;"><?php echo number_format($totalEquity, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="mt-auto border-top border-2 border-primary bg-light px-4 py-3 d-flex justify-content-between align-items-center">
                    <div class="fw-bold text-dark fs-5">إجمالي الخصوم والملكية:</div>
                    <div class="font-monospace fw-bold text-primary-dark fs-5" style="direction:ltr;"><?php echo number_format($totalLiabilitiesAndEquity, 2); ?></div>
                </div>

            </div>
        </div>
        
        <?php 
            $diff = abs($totalAssets - $totalLiabilitiesAndEquity);
            if ($diff > 0.01): 
        ?>
        <div class="alert alert-danger m-0 rounded-0 border-top text-center justify-content-center">
            <i class="fas fa-exclamation-triangle fa-lg"></i> الميزانية غير متزنة! يوجد فرق بقيمة: <strong><?php echo number_format($diff, 2); ?></strong> تأكد من صحة القيود المحاسبية.
        </div>
        <?php else: ?>
        <div class="alert alert-success m-0 rounded-0 border-top text-center justify-content-center">
            <i class="fas fa-check-circle fa-lg"></i> الميزانية العمومية متزنة تماماً.
        </div>
        <?php endif; ?>

    </div>
</div>

<style>
@media print {
    body { background: #fff !important; }
    .d-print-none, .sidebar, .topbar { display: none !important; }
    .main-content { margin: 0 !important; }
    .card { box-shadow: none !important; border: 2px solid #000 !important; max-width: 100% !important; margin: 0 !important;}
    .border-end { border-right: 1px solid #dee2e6 !important; }
}
</style>