<?php
// app/views/reports/index.php
$salesData = isset($data['sales_data']) ? json_encode($data['sales_data']) : '[]';
$expenseLabels = $data['expense_labels'] ?? '[]';
$expenseValues = $data['expense_values'] ?? '[]';
$topCustomers = $data['top_customers'] ?? [];
$topProducts = $data['top_products'] ?? [];
$inventory = $data['inventory_valuation'] ?? [];
$currentYear = $data['current_year'] ?? date('Y');
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-chart-pie text-primary"></i> التقارير الذكية ولوحات القيادة (Dashboards)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تحليل مرئي شامل لبيانات العام المالي <?php echo htmlspecialchars((string)$currentYear); ?></p>
    </div>
    <div class="d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/report/index" method="GET" class="d-flex gap-2">
            <select name="year" class="form-control" onchange="this.form.submit()" style="width:120px;">
                <?php for($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo $currentYear == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
        </form>
        <a href="<?php echo URLROOT; ?>/report/sales" class="btn btn-secondary">
            <i class="fas fa-table"></i> تقرير المبيعات
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> طباعة اللوحة
        </button>
    </div>
</div>

<div class="form-grid mb-4" style="grid-template-columns: 2fr 1fr;">
    <!-- مخطط المبيعات -->
    <div class="card mb-0">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-line text-success"></i> إيرادات المبيعات الشهرية (<?php echo $currentYear; ?>)</h3>
        </div>
        <div class="card-body">
            <div style="height: 300px; width: 100%;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- مخطط المصروفات -->
    <div class="card mb-0">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-pie text-danger"></i> توزيع المصروفات التشغيلية</h3>
        </div>
        <div class="card-body d-flex justify-content-center">
            <div style="height: 300px; width: 100%;">
                <canvas id="expenseChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="form-grid mb-4" style="grid-template-columns: 1fr 1fr;">
    <!-- أفضل العملاء -->
    <div class="card mb-0">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-trophy text-accent"></i> أفضل العملاء من حيث المشتريات</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>العميل</th>
                            <th class="text-center">الفواتير</th>
                            <th class="text-left">إجمالي المشتريات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($topCustomers as $index => $cust): ?>
                        <tr>
                            <td class="fw-bold text-dark">
                                <span class="badge badge-secondary me-2"><?php echo $index + 1; ?></span> <?php echo htmlspecialchars($cust->customer_name); ?>
                            </td>
                            <td class="text-center"><span class="badge badge-info"><?php echo $cust->invoices_count; ?></span></td>
                            <td class="font-monospace fw-bold text-success" style="direction:ltr; text-align:right;"><?php echo number_format($cust->total_purchases, 2); ?> ر.س</td>
                        </tr>
                        <?php endforeach; if(empty($topCustomers)): ?>
                        <tr><td colspan="3" class="text-center text-muted p-4">لا توجد بيانات مبيعات.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- أكثر المنتجات مبيعاً -->
    <div class="card mb-0">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-fire text-danger"></i> أكثر المنتجات مبيعاً (Top Products)</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>المنتج / الصنف</th>
                            <th class="text-center">الكمية المباعة</th>
                            <th class="text-left">إجمالي الإيرادات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($topProducts as $prod): ?>
                        <tr>
                            <td class="fw-bold text-dark"><i class="fas fa-box text-muted me-2"></i> <?php echo htmlspecialchars($prod->name); ?></td>
                            <td class="text-center"><span class="badge badge-primary font-monospace"><?php echo $prod->qty_sold; ?></span></td>
                            <td class="font-monospace fw-bold text-success" style="direction:ltr; text-align:right;"><?php echo number_format($prod->total_revenue, 2); ?> ر.س</td>
                        </tr>
                        <?php endforeach; if(empty($topProducts)): ?>
                        <tr><td colspan="3" class="text-center text-muted p-4">لا توجد بيانات.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- تقييم المخزون -->
<div class="card mb-0">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-boxes-stacked text-info"></i> تقييم المخزون المتاح حسب التصنيف</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>التصنيف</th>
                        <th class="text-center">عدد المنتجات المسجلة</th>
                        <th class="text-center">إجمالي القطع بالمخزن</th>
                        <th class="text-left">القيمة التقديرية الإجمالية</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandValuation = 0;
                    foreach($inventory as $inv): 
                        $grandValuation += $inv->total_value;
                    ?>
                    <tr>
                        <td class="fw-bold text-body"><i class="fas fa-tags text-muted me-2"></i> <?php echo htmlspecialchars($inv->category_name ?? 'بدون تصنيف'); ?></td>
                        <td class="text-center"><?php echo $inv->products_count; ?></td>
                        <td class="text-center font-monospace fw-bold text-primary"><?php echo number_format($inv->total_items); ?></td>
                        <td class="font-monospace fw-bold text-dark" style="direction:ltr; text-align:right;"><?php echo number_format($inv->total_value, 2); ?> ر.س</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="3" class="fw-bold fs-6 p-3">إجمالي قيمة البضائع بالمخازن:</td>
                        <td class="font-monospace fw-bold text-success fs-5 p-3" style="direction:ltr; text-align:right; border-bottom:3px double var(--success);"><?php echo number_format($grandValuation, 2); ?> ر.س</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. رسم مخطط المبيعات الشهري
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
            datasets: [{
                label: 'المبيعات (ر.س)',
                data: <?php echo $salesData; ?>,
                backgroundColor: 'rgba(20, 184, 166, 0.8)',
                borderColor: '#0d9488',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, grid: { color: '#e2e8f0' } }, x: { grid: { display: false } } },
            plugins: { legend: { display: false } }
        }
    });

    // 2. رسم المخطط الدائري للمصروفات
    const expLabels = <?php echo $expenseLabels; ?>;
    const expValues = <?php echo $expenseValues; ?>;
    
    if(expValues.length === 0) {
        expLabels.push('لا توجد مصروفات');
        expValues.push(1);
    }

    const expCtx = document.getElementById('expenseChart').getContext('2d');
    new Chart(expCtx, {
        type: 'doughnut',
        data: {
            labels: expLabels,
            datasets: [{
                data: expValues,
                backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#06b6d4', '#8b5cf6', '#14b8a6', '#64748b'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: { 
                legend: { position: 'right', rtl: true, labels: { font: { family: 'Cairo' } } } 
            }
        }
    });
});
</script>