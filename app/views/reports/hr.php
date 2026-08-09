<?php
// app/views/reports/hr.php
$hrData = $data['hr_data'] ?? [];
$selectedMonth = $data['selected_month'] ?? date('Y-m');
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-users-gear text-secondary"></i> تقرير الموارد البشرية (HR)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">ملخص الموظفين وتقديرات الرواتب للشهر.</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-dark"><i class="fas fa-print"></i> طباعة التقرير</button>
        <a href="<?php echo URLROOT; ?>/report/index" class="btn btn-secondary">رجوع</a>
    </div>
</div>

<div class="card mb-4 d-print-none">
    <div class="card-body bg-light">
        <form action="<?php echo URLROOT; ?>/report/hr" method="GET" class="d-flex gap-3 align-items-end flex-wrap">
            <div style="flex: 1; max-width: 300px;">
                <label class="form-label">اختر الشهر</label>
                <input type="month" name="month" class="form-control" value="<?php echo htmlspecialchars($selectedMonth); ?>" required>
            </div>
            <div>
                <button type="submit" class="btn btn-secondary" style="height: 46px;"><i class="fas fa-sync"></i> جلب البيانات</button>
            </div>
        </form>
    </div>
</div>

<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">تقرير الموارد البشرية والرواتب</h2>
    <h5 class="text-muted font-monospace">الشهر المالي: <?php echo $selectedMonth; ?></h5>
</div>

<div class="form-grid" style="grid-template-columns: 1fr 1fr;">
    <!-- إحصائيات القوى العاملة -->
    <div class="card border-bottom" style="border-bottom-width: 4px; border-bottom-color: var(--secondary);">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="fas fa-users text-secondary"></i> القوى العاملة</h5>
        </div>
        <div class="card-body text-center p-5">
            <div class="text-muted fw-bold mb-2">إجمالي الموظفين النشطين</div>
            <div class="font-monospace fw-bold text-dark" style="font-size: 48px; line-height: 1;"><?php echo $hrData['active_employees'] ?? 0; ?></div>
        </div>
    </div>

    <!-- تقديرات الرواتب -->
    <div class="card border-bottom" style="border-bottom-width: 4px; border-bottom-color: var(--primary);">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="fas fa-money-check-dollar text-primary"></i> مسير الرواتب التقديري</h5>
        </div>
        <div class="card-body p-4">
            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                <span class="text-muted fw-bold">إجمالي الرواتب الأساسية:</span>
                <span class="font-monospace fw-bold text-dark" style="direction:ltr;"><?php echo number_format($hrData['total_basic_salary'] ?? 0, 2); ?> ر.س</span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                <span class="text-muted fw-bold">إجمالي البدلات:</span>
                <span class="font-monospace fw-bold text-info" style="direction:ltr;"><?php echo number_format($hrData['total_allowances'] ?? 0, 2); ?> ر.س</span>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <span class="fw-bold text-dark" style="font-size:18px;">التكلفة التقديرية للرواتب:</span>
                <span class="font-monospace fw-bold text-primary" style="font-size:22px; direction:ltr;"><?php echo number_format($hrData['estimated_payroll'] ?? 0, 2); ?> ر.س</span>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: #fff !important; }
    .d-print-none, .sidebar, .topbar { display: none !important; }
    .main-content { margin: 0 !important; }
    .card { box-shadow: none !important; border: 1px solid var(--border-color) !important; page-break-inside: avoid; }
    .d-print-block { display: block !important; }
}
</style>