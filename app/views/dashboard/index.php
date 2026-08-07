<?php
// app/views/dashboard/index.php
$kpis = $data['kpis'] ?? [];
$stats = $data['stats'] ?? [];
$approvals = $data['approvals'] ?? [];
$alerts = $data['alerts'] ?? [];
$recentActivities = $data['recent_activities'] ?? [];
$userName = $_SESSION['user_name'] ?? 'مدير النظام';
?>

<style>
    /* ==========================================
       Dashboard Specific Styles
       ========================================== */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .dash-fade-up { animation: fadeUp 0.6s ease-out forwards; }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }

    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: var(--radius-lg);
        padding: 32px 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.5);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
    }

    .welcome-banner::before {
        content: '\f200'; /* FontAwesome pie-chart */
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        position: absolute;
        left: -20px;
        bottom: -40px;
        font-size: 200px;
        opacity: 0.03;
        transform: rotate(-15deg);
        pointer-events: none;
    }

    .welcome-text h1 {
        font-size: 26px;
        font-weight: 800;
        margin-bottom: 6px;
        letter-spacing: -0.5px;
    }

    .welcome-text p {
        font-size: 14px;
        color: #94a3b8;
        margin: 0;
        max-width: 500px;
        line-height: 1.6;
    }

    .welcome-actions {
        display: flex;
        gap: 12px;
        position: relative;
        z-index: 2;
    }

    .btn-glass {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        transition: all 0.3s ease;
    }

    .btn-glass:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    /* KPI Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .kpi-card {
        background: var(--card-bg);
        border-radius: var(--radius-lg);
        padding: 24px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .kpi-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }

    .kpi-icon.sales { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25); }
    .kpi-icon.expenses { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #fff; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.25); }
    .kpi-icon.profit { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #fff; box-shadow: 0 4px 15px rgba(14, 165, 233, 0.25); }
    .kpi-icon.receivables { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.25); }

    .kpi-data h4 {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0 0 4px 0;
        line-height: 1;
        direction: ltr;
        text-align: right;
    }

    .kpi-data p {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        margin: 0;
    }

    /* Smart Alerts Banner */
    .smart-alert {
        background: linear-gradient(to left, #fffbeb, #fef3c7);
        border: 1px solid #fde68a;
        border-right: 4px solid #f59e0b;
        border-radius: var(--radius-md);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
    }

    .smart-alert-content {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .smart-alert-icon {
        font-size: 28px;
        color: #d97706;
        animation: pulseAlert 2s infinite;
    }

    @keyframes pulseAlert {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .smart-alert-text strong {
        display: block;
        color: #92400e;
        font-size: 15px;
        margin-bottom: 4px;
    }

    .smart-alert-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .alert-tag {
        background: #fff;
        border: 1px solid #fcd34d;
        color: #b45309;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .alert-tag.danger { border-color: #fca5a5; color: #b91c1c; }

    /* Layout Grid for Chart & Tables */
    .dash-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    /* Modern Card */
    .modern-card {
        background: var(--card-bg);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .mc-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--slate-100);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .mc-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .mc-title i { color: var(--primary); font-size: 18px; }
    .mc-body { padding: 24px; flex: 1; }

    /* Quick Action Buttons */
    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .qa-btn {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        border-radius: var(--radius-md);
        background: var(--slate-50);
        border: 1px solid var(--border-color);
        color: var(--text-dark);
        font-weight: 700;
        font-size: 14px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .qa-btn i {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .qa-btn:hover { background: #fff; transform: translateX(-4px); box-shadow: var(--shadow-md); border-color: var(--primary-light); }
    .qa-btn.qa-1 i { background: var(--success-100); color: var(--success-600); }
    .qa-btn.qa-2 i { background: var(--info-100); color: var(--info-600); }
    .qa-btn.qa-3 i { background: var(--purple-100); color: var(--purple-600); }
    .qa-btn.qa-4 i { background: var(--accent-100); color: var(--accent-600); }

    /* Modern Table */
    .modern-table { width: 100%; border-collapse: collapse; }
    .modern-table th { padding: 12px 20px; font-size: 12px; font-weight: 700; color: var(--text-muted); background: var(--slate-50); border-bottom: 1px solid var(--border-color); text-align: right; }
    .modern-table td { padding: 16px 20px; font-size: 14px; color: var(--text-body); border-bottom: 1px solid var(--slate-100); vertical-align: middle; }
    .modern-table tr:last-child td { border-bottom: none; }
    .modern-table tr:hover td { background: rgba(0,0,0,0.01); }

    @media (max-width: 1024px) {
        .dash-layout { grid-template-columns: 1fr; }
        .welcome-banner { flex-direction: column; align-items: flex-start; }
    }
</style>

<!-- 1. Welcome Banner -->
<div class="welcome-banner dash-fade-up">
    <div class="welcome-text">
        <h1>مرحباً بك مجدداً، <?php echo htmlspecialchars($userName); ?> 👋</h1>
        <p>إليك نظرة شاملة وذكية على أداء المؤسسة المالي والتشغيلي اليوم. جميع الأنظمة تعمل بكفاءة.</p>
    </div>
    <div class="welcome-actions">
        <a href="<?php echo URLROOT; ?>/sale/create" class="btn btn-primary"><i class="fas fa-plus"></i> فاتورة مبيعات</a>
        <a href="<?php echo URLROOT; ?>/report/index" class="btn btn-glass"><i class="fas fa-chart-pie"></i> لوحة التقارير</a>
    </div>
</div>

<!-- 2. KPI Cards -->
<div class="kpi-grid dash-fade-up delay-1">
    <div class="kpi-card">
        <div class="kpi-icon sales"><i class="fas fa-wallet"></i></div>
        <div class="kpi-data">
            <h4 class="font-monospace"><?php echo number_format($kpis['sales'] ?? 0); ?></h4>
            <p>المبيعات الكلية (ر.س)</p>
        </div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-icon expenses"><i class="fas fa-money-bill-transfer"></i></div>
        <div class="kpi-data">
            <h4 class="font-monospace"><?php echo number_format($kpis['expenses'] ?? 0); ?></h4>
            <p>إجمالي المصروفات</p>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon profit"><i class="fas fa-chart-line"></i></div>
        <div class="kpi-data">
            <?php 
                $profit = $kpis['profit'] ?? 0;
                $profitColor = $profit >= 0 ? 'var(--info)' : 'var(--danger)';
            ?>
            <h4 class="font-monospace" style="color: <?php echo $profitColor; ?>;"><?php echo number_format($profit); ?></h4>
            <p>صافي الدخل التقريبي</p>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon receivables"><i class="fas fa-hand-holding-dollar"></i></div>
        <div class="kpi-data">
            <h4 class="font-monospace"><?php echo number_format($kpis['receivables'] ?? 0); ?></h4>
            <p>مستحقات العملاء</p>
        </div>
    </div>
</div>

<!-- 3. Smart Alerts -->
<?php 
$totalAlerts = ($approvals['leaves'] ?? 0) + ($approvals['advances'] ?? 0) + ($approvals['prs'] ?? 0) + ($alerts['low_stock'] ?? 0) + ($alerts['expiring_contracts'] ?? 0);
if ($totalAlerts > 0): 
?>
<div class="smart-alert dash-fade-up delay-2">
    <div class="smart-alert-content">
        <div class="smart-alert-icon"><i class="fas fa-bell"></i></div>
        <div class="smart-alert-text">
            <strong>يوجد مهام وتنبيهات تتطلب انتباهك:</strong>
            <div class="smart-alert-tags">
                <?php if (($approvals['leaves'] ?? 0) > 0): ?><span class="alert-tag"><i class="fas fa-calendar-minus"></i> إجازات (<?php echo $approvals['leaves']; ?>)</span><?php endif; ?>
                <?php if (($approvals['advances'] ?? 0) > 0): ?><span class="alert-tag"><i class="fas fa-money-bill"></i> سلف (<?php echo $approvals['advances']; ?>)</span><?php endif; ?>
                <?php if (($approvals['prs'] ?? 0) > 0): ?><span class="alert-tag"><i class="fas fa-cart-shopping"></i> طلبات شراء (<?php echo $approvals['prs']; ?>)</span><?php endif; ?>
                <?php if (($alerts['low_stock'] ?? 0) > 0): ?><span class="alert-tag danger"><i class="fas fa-box-open"></i> نواقص مخزون (<?php echo $alerts['low_stock']; ?>)</span><?php endif; ?>
                <?php if (($alerts['expiring_contracts'] ?? 0) > 0): ?><span class="alert-tag danger"><i class="fas fa-file-contract"></i> عقود تنتهي (<?php echo $alerts['expiring_contracts']; ?>)</span><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- 4. Main Content (Chart & Quick Actions) -->
<div class="dash-layout dash-fade-up delay-3">
    
    <!-- Chart Section -->
    <div class="modern-card">
        <div class="mc-header">
            <h3 class="mc-title"><i class="fas fa-chart-area"></i> تحليل المبيعات (آخر 6 أشهر)</h3>
            <a href="<?php echo URLROOT; ?>/report/sales" class="btn btn-sm btn-secondary">تقرير مفصل</a>
        </div>
        <div class="mc-body">
            <div style="height: 300px; width: 100%;">
                <canvas id="dashboardChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="modern-card">
        <div class="mc-header">
            <h3 class="mc-title"><i class="fas fa-bolt" style="color: var(--accent);"></i> إجراءات سريعة</h3>
        </div>
        <div class="mc-body">
            <div class="quick-actions">
                <a href="<?php echo URLROOT; ?>/sale/create" class="qa-btn qa-1">
                    <i class="fas fa-file-invoice-dollar"></i> إصدار فاتورة مبيعات
                </a>
                <a href="<?php echo URLROOT; ?>/purchase/create" class="qa-btn qa-2">
                    <i class="fas fa-cart-plus"></i> إنشاء أمر شراء (PO)
                </a>
                <a href="<?php echo URLROOT; ?>/journal/create" class="qa-btn qa-3">
                    <i class="fas fa-pen-nib"></i> تسجيل قيد يومية
                </a>
                <a href="<?php echo URLROOT; ?>/customer/create" class="qa-btn qa-4">
                    <i class="fas fa-user-plus"></i> إضافة عميل جديد
                </a>
            </div>
        </div>
    </div>

</div>

<!-- 5. Recent Activity Table -->
<div class="modern-card mt-4 dash-fade-up delay-3">
    <div class="mc-header">
        <h3 class="mc-title"><i class="fas fa-history text-success"></i> أحدث الفواتير المصدرة</h3>
        <a href="<?php echo URLROOT; ?>/sale/index" class="btn btn-sm btn-secondary">سجل الفواتير</a>
    </div>
    <div class="mc-body p-0">
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>التاريخ والوقت</th>
                        <th class="text-left">المبلغ الإجمالي</th>
                        <th class="text-center">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentActivities)): foreach ($recentActivities as $act): ?>
                    <tr>
                        <td>
                            <div class="font-monospace fw-bold text-dark" style="font-size: 14px; background: var(--slate-100); padding: 4px 10px; border-radius: 6px; display: inline-block;">
                                <?php echo htmlspecialchars($act->title); ?>
                            </div>
                        </td>
                        <td class="text-muted" style="font-size: 13px;"><i class="far fa-clock"></i> <?php echo date('Y-m-d h:i A', strtotime($act->created_at)); ?></td>
                        <td class="font-monospace fw-bold text-success text-left" style="font-size: 15px; direction: ltr;">
                            +<?php echo number_format($act->details, 2); ?> ر.س
                        </td>
                        <td class="text-center">
                            <a href="<?php echo URLROOT; ?>/sale/index" class="btn-icon view"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted" style="padding: 40px;">لا توجد أنشطة مسجلة مؤخراً في النظام.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('dashboardChart');
    if (ctx) {
        // Gradient fill for the chart
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(20, 184, 166, 0.3)'); // Primary color light
        gradient.addColorStop(1, 'rgba(20, 184, 166, 0.0)');

        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo $data['monthly_sales_labels'] ?? '[]'; ?>,
                datasets: [{
                    label: 'المبيعات (ر.س)',
                    data: <?php echo $data['monthly_sales_data'] ?? '[]'; ?>,
                    borderColor: '#14b8a6', // Primary color
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#14b8a6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Smooth curves
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { family: 'Cairo', size: 13 },
                        bodyFont: { family: 'Cairo', size: 14, weight: 'bold' },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString('ar-SA') + ' ر.س';
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: { font: { family: 'monospace' }, color: '#94a3b8' }
                    },
                    x: { 
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { family: 'Cairo' }, color: '#64748b' }
                    }
                }
            }
        });
    }
});
</script>