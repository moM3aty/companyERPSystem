<?php
// المسار: app/views/reports/index.php
/** @var array $data */
$data = $data ?? [];
$salesData = isset($data['sales_data']) ? json_encode($data['sales_data']) : '[]';
$expenseLabels = $data['expense_labels'] ?? '[]';
$expenseValues = $data['expense_values'] ?? '[]';
$topCustomers = $data['top_customers'] ?? [];
$inventory = $data['inventory_valuation'] ?? [];
$currentYear = $data['current_year'] ?? date('Y');
?>

<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-chart-pie" style="color:var(--primary);"></i> التقارير الذكية ولوحات القيادة (Dashboards)
        </h3>
        <p style="margin:4px 0 0; font-size:13px; color:var(--text-muted);">تحليل مرئي شامل لبيانات العام المالي <?php echo htmlspecialchars((string)$currentYear); ?></p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="<?php echo URL_ROOT; ?>/report/sales" style="padding:10px 20px; background:var(--card-bg); border:1px solid var(--border); color:var(--text-dark); border-radius:8px; text-decoration:none; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; box-shadow:var(--shadow-sm);">
            <i class="fas fa-table"></i> تقرير المبيعات الجدولي
        </a>
        <button onclick="window.print()" style="padding:10px 20px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:700; font-size:13px; cursor:pointer; box-shadow:0 4px 10px rgba(20,184,166,0.2);">
            <i class="fas fa-print"></i> طباعة (PDF)
        </button>
    </div>
</div>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px; margin-bottom:24px;">
    
    <!-- مخطط المبيعات (Bar Chart) -->
    <div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); padding:24px;">
        <h4 style="margin:0 0 20px; font-size:16px; font-weight:700; color:var(--text-dark);"><i class="fas fa-chart-line" style="color:var(--success);"></i> إيرادات المبيعات الشهرية</h4>
        <div style="height: 300px; width: 100%;">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- مخطط المصروفات (Doughnut Chart) -->
    <div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); padding:24px;">
        <h4 style="margin:0 0 20px; font-size:16px; font-weight:700; color:var(--text-dark);"><i class="fas fa-chart-pie" style="color:var(--danger);"></i> توزيع المصروفات</h4>
        <div style="height: 300px; width: 100%; display:flex; justify-content:center;">
            <canvas id="expenseChart"></canvas>
        </div>
    </div>

</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">
    
    <!-- أفضل العملاء -->
    <div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
        <div style="padding:20px; border-bottom:1px solid var(--border); background:#f8fafc;">
            <h4 style="margin:0; font-size:15px; font-weight:700; color:var(--text-dark);"><i class="fas fa-trophy" style="color:var(--accent);"></i> أفضل العملاء (Top Customers)</h4>
        </div>
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#fff; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:12px 20px; font-size:11px; color:var(--text-muted);">اسم العميل</th>
                    <th style="padding:12px 20px; font-size:11px; color:var(--text-muted); text-align:center;">الفواتير</th>
                    <th style="padding:12px 20px; font-size:11px; color:var(--text-muted);">إجمالي الشراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($topCustomers as $cust): ?>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 20px; font-weight:700; color:var(--text-dark); font-size:13px;"><?php echo htmlspecialchars($cust->customer_name); ?></td>
                    <td style="padding:12px 20px; text-align:center; font-size:13px;"><span style="background:var(--page-bg); padding:2px 8px; border-radius:20px; border:1px solid var(--border);"><?php echo $cust->invoices_count; ?></span></td>
                    <td style="padding:12px 20px; font-family:monospace; font-weight:800; color:var(--success); direction:ltr; text-align:right;"><?php echo number_format($cust->total_purchases, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- تقييم المخزون -->
    <div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
        <div style="padding:20px; border-bottom:1px solid var(--border); background:#f8fafc;">
            <h4 style="margin:0; font-size:15px; font-weight:700; color:var(--text-dark);"><i class="fas fa-boxes-stacked" style="color:var(--info);"></i> تقييم المخزون حسب التصنيف</h4>
        </div>
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#fff; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:12px 20px; font-size:11px; color:var(--text-muted);">التصنيف</th>
                    <th style="padding:12px 20px; font-size:11px; color:var(--text-muted); text-align:center;">الكمية المتوفرة</th>
                    <th style="padding:12px 20px; font-size:11px; color:var(--text-muted);">القيمة الإجمالية</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($inventory as $inv): ?>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 20px; font-weight:600; color:var(--text-body); font-size:13px;"><?php echo htmlspecialchars($inv->category_name ?? 'غير مصنف'); ?></td>
                    <td style="padding:12px 20px; text-align:center; font-size:13px; font-weight:700;"><?php echo number_format($inv->total_items); ?></td>
                    <td style="padding:12px 20px; font-family:monospace; font-weight:800; color:var(--primary-dark); direction:ltr; text-align:right;"><?php echo number_format($inv->total_value, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. رسم مخطط المبيعات
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
            scales: {
                y: { beginAtZero: true, grid: { color: '#e2e8f0' } },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // 2. رسم مخطط المصروفات
    const expLabels = <?php echo $expenseLabels; ?>;
    const expValues = <?php echo $expenseValues; ?>;
    
    // في حال عدم وجود مصروفات
    if(expValues.length === 0) {
        expLabels.push('لا توجد بيانات');
        expValues.push(1);
    }

    const expCtx = document.getElementById('expenseChart').getContext('2d');
    new Chart(expCtx, {
        type: 'doughnut',
        data: {
            labels: expLabels,
            datasets: [{
                data: expValues,
                backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#06b6d4', '#8b5cf6'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Cairo' } } }
            }
        }
    });
});
</script>