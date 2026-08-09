<?php
// app/views/reports/purchases.php
$purchases = $data['purchases'] ?? [];
$suppliers = $data['suppliers'] ?? [];
$startDate = $data['start_date'] ?? '';
$endDate = $data['end_date'] ?? '';
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-boxes-packing text-info"></i> تقرير المشتريات والموردين</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع أوامر الشراء، المرتجعات، وتصنيف الموردين حسب حجم التعامل.</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-dark"><i class="fas fa-print"></i> طباعة التقرير</button>
        <a href="<?php echo URLROOT; ?>/report/index" class="btn btn-secondary">رجوع</a>
    </div>
</div>

<div class="card mb-4 d-print-none">
    <div class="card-body bg-light">
        <form action="<?php echo URLROOT; ?>/report/purchases" method="GET" class="d-flex gap-3 align-items-end flex-wrap">
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">من تاريخ</label>
                <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>" required>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>" required>
            </div>
            <div>
                <button type="submit" class="btn btn-info" style="height: 46px;"><i class="fas fa-filter"></i> تحديث التقرير</button>
            </div>
        </form>
    </div>
</div>

<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">تقرير المشتريات والمرتجعات</h2>
    <h5 class="text-muted font-monospace">الفترة: <?php echo $startDate; ?> إلى <?php echo $endDate; ?></h5>
</div>

<!-- ملخص المشتريات -->
<div class="form-grid mb-4" style="grid-template-columns: repeat(3, 1fr);">
    <div class="card mb-0 border-bottom" style="border-bottom-width: 4px; border-bottom-color: var(--info);">
        <div class="card-body text-center p-3">
            <div class="text-muted fw-bold mb-2">إجمالي المشتريات (<?php echo $purchases['total_orders'] ?? 0; ?> أمر)</div>
            <div class="font-monospace fs-3 fw-bold text-info" style="direction:ltr;"><?php echo number_format($purchases['total_purchases'] ?? 0, 2); ?></div>
        </div>
    </div>
    <div class="card mb-0 border-bottom" style="border-bottom-width: 4px; border-bottom-color: var(--danger);">
        <div class="card-body text-center p-3">
            <div class="text-muted fw-bold mb-2">إجمالي المرتجعات (<?php echo $purchases['total_returns'] ?? 0; ?> حركة)</div>
            <div class="font-monospace fs-3 fw-bold text-danger" style="direction:ltr;">-<?php echo number_format($purchases['total_returned_amount'] ?? 0, 2); ?></div>
        </div>
    </div>
    <div class="card mb-0 border-bottom" style="border-bottom-width: 4px; border-bottom-color: var(--success);">
        <div class="card-body text-center p-3">
            <div class="text-muted fw-bold mb-2">صافي المشتريات</div>
            <div class="font-monospace fs-3 fw-bold text-success" style="direction:ltr;"><?php echo number_format($purchases['net_purchases'] ?? 0, 2); ?></div>
        </div>
    </div>
</div>

<!-- كبار الموردين -->
<div class="card">
    <div class="card-header bg-light">
        <h3 class="card-title"><i class="fas fa-truck text-secondary"></i> أكبر الموردين تعاملاً خلال الفترة</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-white">
                    <tr>
                        <th style="width: 50%;">اسم المورد</th>
                        <th class="text-center">عدد الأوامر</th>
                        <th class="text-left">إجمالي التعامل (ر.س)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($suppliers as $sup): ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="fas fa-building text-muted me-2"></i> <?php echo htmlspecialchars($sup->supplier_name); ?></td>
                        <td class="text-center font-monospace fw-bold fs-5 text-muted"><?php echo $sup->order_count; ?></td>
                        <td class="font-monospace fw-bold text-info text-left fs-5" style="direction:ltr;"><?php echo number_format($sup->total_amount, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($suppliers)): ?>
                    <tr><td colspan="3" class="text-center text-muted p-5"><i class="fas fa-truck-loading fa-3x mb-3 opacity-50 d-block"></i> لا يوجد تعاملات مع موردين في هذه الفترة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: #fff !important; }
    .d-print-none, .sidebar, .topbar { display: none !important; }
    .main-content { margin: 0 !important; }
    .card { box-shadow: none !important; border: 1px solid var(--border-color) !important; margin-bottom: 20px !important; page-break-inside: avoid; }
    .d-print-block { display: block !important; }
}
</style>