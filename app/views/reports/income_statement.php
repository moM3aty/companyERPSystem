<?php
// app/views/reports/income_statement.php
$incomeData = $data['income_data'] ?? null;
$startDate = $data['start_date'] ?? null;
$endDate = $data['end_date'] ?? null;

$netIncomeClass = $incomeData['net_income'] >= 0 ? 'text-success' : 'text-danger';
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-primary"></i> قائمة الدخل (Income Statement)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">بيان الأرباح والخسائر للشركة خلال فترة محددة.</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-dark"><i class="fas fa-print"></i> طباعة التقرير</button>
        <a href="<?php echo URLROOT; ?>/report/index" class="btn btn-secondary">رجوع</a>
    </div>
</div>

<div class="card mb-4 d-print-none">
    <div class="card-body bg-light">
        <form action="<?php echo URLROOT; ?>/report/incomeStatement" method="GET" class="d-flex gap-3 align-items-end flex-wrap">
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">من تاريخ</label>
                <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>" required>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>" required>
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="height: 46px;"><i class="fas fa-filter"></i> تحديث التقرير</button>
            </div>
        </form>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto; box-shadow: var(--shadow-md);">
    <div class="card-header bg-white text-center border-bottom pb-4 pt-4">
        <h2 class="fw-black text-dark mb-1">قائمة الدخل (Profit & Loss)</h2>
        <h5 class="text-muted font-monospace mb-0">للفترة من: <?php echo $startDate; ?> إلى <?php echo $endDate; ?></h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-borderless mb-0">
            <tbody>
                <!-- الإيرادات -->
                <tr class="bg-light border-bottom">
                    <td colspan="2"><h5 class="fw-bold text-primary mb-0"><i class="fas fa-arrow-trend-up"></i> الإيرادات (Revenues)</h5></td>
                </tr>
                <tr>
                    <td class="ps-4 fw-bold text-dark">إجمالي إيرادات المبيعات والخدمات</td>
                    <td class="text-left font-monospace fs-5 text-dark" style="direction:ltr;"><?php echo number_format($incomeData['revenue'], 2); ?></td>
                </tr>
                <tr class="border-bottom">
                    <td class="text-left fw-bold text-muted">إجمالي الإيرادات:</td>
                    <td class="text-left font-monospace fw-bold text-primary fs-5" style="direction:ltr;"><?php echo number_format($incomeData['revenue'], 2); ?></td>
                </tr>

                <!-- المصروفات -->
                <tr class="bg-light border-bottom">
                    <td colspan="2"><h5 class="fw-bold text-danger mb-0"><i class="fas fa-arrow-trend-down"></i> المصروفات (Expenses)</h5></td>
                </tr>
                <tr>
                    <td class="ps-4 fw-bold text-dark">إجمالي المصروفات التشغيلية (الرواتب، المشتريات، الإيجارات، إلخ)</td>
                    <td class="text-left font-monospace fs-5 text-dark" style="direction:ltr;"><?php echo number_format($incomeData['expenses'], 2); ?></td>
                </tr>
                <tr class="border-bottom border-dark">
                    <td class="text-left fw-bold text-muted">إجمالي المصروفات:</td>
                    <td class="text-left font-monospace fw-bold text-danger fs-5" style="direction:ltr;">(<?php echo number_format($incomeData['expenses'], 2); ?>)</td>
                </tr>

                <!-- صافي الدخل -->
                <tr class="bg-slate-50">
                    <td class="py-4"><h4 class="fw-black text-dark mb-0">صافي الدخل (Net Income)</h4></td>
                    <td class="py-4 text-left font-monospace fw-black fs-3 <?php echo $netIncomeClass; ?>" style="direction:ltr;"><?php echo number_format($incomeData['net_income'], 2); ?> <span class="fs-6 text-muted">SAR</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    body { background: #fff !important; }
    .d-print-none, .sidebar, .topbar { display: none !important; }
    .main-content { margin: 0 !important; }
    .card { box-shadow: none !important; border: 1px solid #000 !important; }
}
</style>