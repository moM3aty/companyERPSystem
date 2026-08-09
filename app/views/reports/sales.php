<?php
// app/views/reports/sales.php
$sales = $data['sales'] ?? [];
$topProducts = $data['top_products'] ?? [];
$startDate = $data['start_date'] ?? '';
$endDate = $data['end_date'] ?? '';

// Fetch Tax Rate dynamically from settings (default 15%)
$db = Database::getInstance();
$cid = Session::get('company_id') ?: 1;
$db->query("SELECT setting_value FROM settings WHERE setting_key = 'tax_rate' AND (company_id = :cid OR company_id IS NULL)");
$db->bind(':cid', $cid);
$taxSetting = $db->single();
$taxRate = $taxSetting ? (float)$taxSetting->setting_value : 15;

$totalRevenue = 0;
$totalInvoices = 0;
foreach($sales as $s) {
    $totalRevenue += (float)$s->total_sales;
    $totalInvoices += (int)$s->total_invoices;
}

// Tax Calculations (Assuming totalRevenue is inclusive of tax or exclusive based on standard, here we treat it as taxable amount for the summary)
$taxAmount = $totalRevenue * ($taxRate / 100);
$grandTotal = $totalRevenue + $taxAmount;
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-success"></i> التقرير المالي للمبيعات والضرائب</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تقرير مفصل يشمل الإيرادات، الضرائب، والأصناف الأكثر مبيعاً.</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-dark"><i class="fas fa-print"></i> طباعة A4</button>
        <a href="<?php echo URLROOT; ?>/report/index" class="btn btn-secondary">رجوع</a>
    </div>
</div>

<!-- 1. فلترة التاريخ (لا تظهر في الطباعة) -->
<div class="card mb-4 d-print-none">
    <div class="card-body bg-light">
        <form action="<?php echo URLROOT; ?>/report/sales" method="GET" class="d-flex gap-3 align-items-end flex-wrap">
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">من تاريخ (Start Date)</label>
                <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>" required>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">إلى تاريخ (End Date)</label>
                <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>" required>
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="height: 46px;"><i class="fas fa-filter"></i> تحديث التقرير</button>
            </div>
        </form>
    </div>
</div>

<!-- هيدر خاص للطباعة فقط -->
<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">تقرير المبيعات والضرائب</h2>
    <h5 class="text-muted font-monospace">الفترة: <?php echo $startDate; ?> إلى <?php echo $endDate; ?></h5>
</div>

<!-- 2. ملخص الأرقام والضرائب -->
<div class="form-grid mb-4" style="grid-template-columns: repeat(4, 1fr);">
    <div class="card mb-0 border-bottom" style="border-bottom-width: 4px; border-bottom-color: var(--primary);">
        <div class="card-body text-center p-3">
            <div class="text-muted fw-bold mb-2" style="font-size:12px;">إجمالي الفواتير المُصدرة</div>
            <div class="font-monospace fs-3 fw-bold text-primary"><?php echo $totalInvoices; ?></div>
        </div>
    </div>
    <div class="card mb-0 border-bottom" style="border-bottom-width: 4px; border-bottom-color: var(--secondary);">
        <div class="card-body text-center p-3">
            <div class="text-muted fw-bold mb-2" style="font-size:12px;">المبيعات (خاضع للضريبة)</div>
            <div class="font-monospace fs-4 fw-bold text-dark" style="direction:ltr;"><?php echo number_format($totalRevenue, 2); ?></div>
        </div>
    </div>
    <div class="card mb-0 border-bottom" style="border-bottom-width: 4px; border-bottom-color: var(--danger);">
        <div class="card-body text-center p-3">
            <div class="text-muted fw-bold mb-2" style="font-size:12px;">قيمة الضريبة المضافة (<?php echo $taxRate; ?>%)</div>
            <div class="font-monospace fs-4 fw-bold text-danger" style="direction:ltr;"><?php echo number_format($taxAmount, 2); ?></div>
        </div>
    </div>
    <div class="card mb-0 border-bottom" style="border-bottom-width: 4px; border-bottom-color: var(--success);">
        <div class="card-body text-center p-3">
            <div class="text-muted fw-bold mb-2" style="font-size:12px;">الإجمالي (شامل الضريبة)</div>
            <div class="font-monospace fs-3 fw-bold text-success" style="direction:ltr;"><?php echo number_format($grandTotal, 2); ?></div>
        </div>
    </div>
</div>

<div class="content-grid" style="grid-template-columns: 2fr 1fr;">
    
    <!-- 3. الجدول التفصيلي اليومي -->
    <div class="card mb-0">
        <div class="card-header bg-light">
            <h3 class="card-title"><i class="fas fa-calendar-day text-secondary"></i> حركة المبيعات اليومية</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-white">
                        <tr>
                            <th>التاريخ</th>
                            <th class="text-center">العمليات (الفواتير)</th>
                            <th class="text-left">قيمة المبيعات (ر.س)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($sales as $s): ?>
                        <tr>
                            <td class="fw-bold text-dark font-monospace"><i class="far fa-calendar-alt text-muted me-2"></i> <?php echo date('Y-m-d', strtotime($s->sale_date)); ?></td>
                            <td class="text-center font-monospace fw-bold fs-5 text-muted"><?php echo $s->total_invoices; ?></td>
                            <td class="font-monospace fw-bold text-success text-left fs-5" style="direction:ltr;"><?php echo number_format($s->total_sales, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($sales)): ?>
                        <tr><td colspan="3" class="text-center text-muted p-5"><i class="fas fa-folder-open fa-3x mb-3 opacity-50 d-block"></i> لا توجد مبيعات في الفترة المحددة.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 4. المنتجات الأكثر مبيعاً -->
    <div class="card mb-0">
        <div class="card-header bg-light">
            <h3 class="card-title"><i class="fas fa-trophy text-warning"></i> الأصناف الأكثر مبيعاً</h3>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush" style="border-radius: 0; padding:0; margin:0; list-style:none;">
                <?php foreach($topProducts as $idx => $tp): 
                    $rankColor = 'transparent';
                    if ($idx === 0) $rankColor = '#fbbf24'; // Gold
                    elseif ($idx === 1) $rankColor = '#94a3b8'; // Silver
                    elseif ($idx === 2) $rankColor = '#b45309'; // Bronze
                ?>
                <li style="padding: 15px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 30px; height: 30px; border-radius: 50%; background: <?php echo $rankColor; ?>; border: 2px solid var(--border-color); display: flex; align-items: center; justify-content: center; font-weight: bold; color: <?php echo $idx < 3 ? '#fff' : 'var(--slate-500)'; ?>;">
                            <?php echo $idx + 1; ?>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:13px;"><?php echo htmlspecialchars($tp->name); ?></div>
                            <div class="text-muted font-monospace" style="font-size:11px;">مباع: <?php echo $tp->total_qty; ?> وحدة</div>
                        </div>
                    </div>
                    <div class="font-monospace fw-bold text-primary" style="direction:ltr; font-size:13px;">
                        <?php echo number_format($tp->total_revenue, 0); ?> ر.س
                    </div>
                </li>
                <?php endforeach; ?>

                <?php if(empty($topProducts)): ?>
                <li style="padding: 20px; text-align: center; color: var(--text-muted);">لا توجد بيانات متاحة للمنتجات.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

</div>

<style>
@media print {
    body { background: #fff !important; }
    .d-print-none, .sidebar, .topbar { display: none !important; }
    .main-content { margin: 0 !important; }
    .content-grid { display: block !important; }
    .card { box-shadow: none !important; border: 1px solid var(--border-color) !important; margin-bottom: 20px !important; page-break-inside: avoid; }
    .d-print-block { display: block !important; }
}
</style>