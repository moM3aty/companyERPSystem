<?php
// app/views/superadmin/dashboard.php
$data = $data ?? [];
$metrics = $data['metrics'] ?? [];
$chartData = $data['chartData'] ?? [];
$packageDist = $data['packageDist'] ?? [];
$recentCompanies = $data['recentCompanies'] ?? [];
?>

<style>
    .saas-kpi-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .saas-kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .kpi-dark {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border: none;
    }
    .kpi-title {
        font-size: 14px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .kpi-dark .kpi-title {
        color: #94a3b8;
    }
    .kpi-value {
        font-size: 32px;
        font-weight: 900;
        font-family: 'Fira Code', monospace;
        letter-spacing: -1px;
    }
    .kpi-dark .kpi-value {
        color: #38bdf8;
    }
    .growth-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 800;
        font-family: monospace;
    }
    .growth-up { background: #dcfce7; color: #166534; }
    .growth-down { background: #fee2e2; color: #991b1b; }
    
    .chart-container-premium {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    
    .premium-table th { background: #f8fafc; color: #475569; font-weight: 800; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
    .premium-table td { vertical-align: middle; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-crown text-warning"></i> لوحة المالك (SaaS Intelligence)</h3>
        <p class="text-muted mt-1" style="font-size: 14px;">نظرة شاملة على أداء ومبيعات نظام الـ ERP السحابي الخاص بك.</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> طباعة التقرير</button>
        <a href="<?php echo URLROOT; ?>/company/create" class="btn btn-primary"><i class="fas fa-building-flag"></i> تسجيل شركة جديدة</a>
    </div>
</div>

<div class="form-grid mb-4" style="grid-template-columns: repeat(4, 1fr);">
    <!-- MRR Card -->
    <div class="saas-kpi-card kpi-dark">
        <div class="kpi-title"><i class="fas fa-sack-dollar text-primary"></i> الإيرادات الشهرية (MRR)</div>
        <div class="kpi-value"><?php echo number_format($metrics['mrr'] ?? 0, 2); ?> <span style="font-size:14px; color:#cbd5e1;">ر.س</span></div>
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <span style="font-size:12px; color:#94a3b8;">مقارنة بالشهر الماضي</span>
            <?php 
                $growth = $metrics['mrr_growth'] ?? 0;
                $gClass = $growth >= 0 ? 'growth-up' : 'growth-down';
                $gIcon = $growth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down';
            ?>
            <span class="growth-badge <?php echo $gClass; ?>"><i class="fas <?php echo $gIcon; ?>"></i> <?php echo abs($growth); ?>%</span>
        </div>
        <!-- خلفية زخرفية -->
        <i class="fas fa-chart-line position-absolute" style="font-size: 100px; color: rgba(255,255,255,0.03); right: -10px; bottom: -20px;"></i>
    </div>

    <!-- ARR Card -->
    <div class="saas-kpi-card">
        <div class="kpi-title text-success"><i class="fas fa-money-bill-trend-up"></i> الإيرادات السنوية المتوقعة (ARR)</div>
        <div class="kpi-value text-dark"><?php echo number_format($metrics['arr'] ?? 0, 2); ?> <span style="font-size:14px; color:var(--text-muted);">ر.س</span></div>
        <div class="mt-3">
            <span class="badge badge-success" style="font-size:11px;">معدل الأمان المالي السنوي</span>
        </div>
    </div>

    <!-- Active Tenants -->
    <div class="saas-kpi-card">
        <div class="kpi-title text-info"><i class="fas fa-buildings"></i> الشركات النشطة (Tenants)</div>
        <div class="kpi-value text-dark"><?php echo number_format($metrics['active_tenants'] ?? 0); ?></div>
        <div class="mt-3">
            <span class="text-muted" style="font-size:12px;">مؤسسات تعتمد على نظامك حالياً</span>
        </div>
    </div>

    <!-- Churn / Suspended -->
    <div class="saas-kpi-card">
        <div class="kpi-title text-danger"><i class="fas fa-user-slash"></i> معدل الفقد (Churn / Suspended)</div>
        <div class="kpi-value text-dark"><?php echo number_format($metrics['suspended_tenants'] ?? 0); ?></div>
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:12px;">شركات أوقفت اشتراكها</span>
            <a href="<?php echo URLROOT; ?>/company/index" style="font-size:12px; font-weight:bold; color:var(--danger);">مراجعة الانقطاع</a>
        </div>
    </div>
</div>

<div class="form-grid mb-4" style="grid-template-columns: 2fr 1fr;">
    <!-- مخطط نمو الإيرادات -->
    <div class="chart-container-premium">
        <h4 class="mb-4" style="font-size: 16px; font-weight: 800; color: #0f172a;"><i class="fas fa-chart-area text-primary me-2"></i> نمو الإيرادات المتكررة (آخر 6 أشهر)</h4>
        <div style="height: 300px; width: 100%;">
            <canvas id="mrrGrowthChart"></canvas>
        </div>
    </div>

    <!-- توزيع الباقات -->
    <div class="chart-container-premium">
        <h4 class="mb-4" style="font-size: 16px; font-weight: 800; color: #0f172a;"><i class="fas fa-chart-pie text-accent me-2"></i> توزيع المشتركين على الباقات</h4>
        <div style="height: 250px; width: 100%; display:flex; justify-content:center;">
            <canvas id="packagesChart"></canvas>
        </div>
        <div class="mt-4">
            <?php foreach($packageDist as $pkg): ?>
                <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 13px;">
                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($pkg->name); ?></span>
                    <span class="badge badge-secondary font-monospace"><?php echo $pkg->companies_count; ?> مؤسسات</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="chart-container-premium p-0 overflow-hidden">
    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
        <h4 class="mb-0" style="font-size: 16px; font-weight: 800; color: #0f172a;"><i class="fas fa-bolt text-warning me-2"></i> أحدث المؤسسات المنضمة للنظام</h4>
        <a href="<?php echo URLROOT; ?>/company/index" class="btn btn-sm btn-secondary">إدارة جميع الشركات</a>
    </div>
    <div class="table-responsive">
        <table class="table premium-table mb-0">
            <thead>
                <tr>
                    <th>اسم المؤسسة / الدومين</th>
                    <th>باقة الاشتراك</th>
                    <th>قيمة الاشتراك</th>
                    <th>تاريخ الانضمام</th>
                    <th class="text-center">الحالة</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($recentCompanies)): foreach($recentCompanies as $comp): 
                    $isSuspended = ($comp->status === 'suspended');
                ?>
                <tr>
                    <td>
                        <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($comp->name); ?></div>
                        <div class="text-muted mt-1 font-monospace" style="font-size: 11px;"><i class="fas fa-link"></i> <?php echo htmlspecialchars($comp->domain ?? 'بدون دومين'); ?></div>
                    </td>
                    <td>
                        <span class="badge badge-info fs-6"><i class="fas fa-box"></i> <?php echo htmlspecialchars($comp->package_name ?? 'الأساسية'); ?></span>
                    </td>
                    <td class="font-monospace fw-bold text-success" style="direction:ltr; text-align:right;">
                        <?php echo number_format($comp->price_monthly ?? 0, 2); ?> ر.س
                    </td>
                    <td class="text-muted font-monospace" style="font-size: 13px;">
                        <i class="far fa-calendar text-primary"></i> <?php echo date('Y-m-d', strtotime($comp->created_at)); ?>
                    </td>
                    <td class="text-center">
                        <span class="badge <?php echo $isSuspended ? 'badge-danger' : 'badge-success'; ?>">
                            <?php echo $isSuspended ? 'موقوفة' : 'نشطة'; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" class="text-center p-5 text-muted">
                        <i class="fas fa-building-circle-exclamation fa-2x mb-3 opacity-50 d-block"></i> لا توجد شركات مسجلة في النظام بعد.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. MRR Growth Line Chart
    const mrrCtx = document.getElementById('mrrGrowthChart').getContext('2d');
    
    // Create a beautiful gradient for the line chart
    const gradient = mrrCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(14, 165, 233, 0.4)'); // Primary blue light
    gradient.addColorStop(1, 'rgba(14, 165, 233, 0.0)');

    new Chart(mrrCtx, {
        type: 'line',
        data: {
            labels: <?php echo $chartData['labels'] ?? '[]'; ?>,
            datasets: [{
                label: 'الإيرادات المتكررة MRR (ر.س)',
                data: <?php echo $chartData['mrr'] ?? '[]'; ?>,
                borderColor: '#0ea5e9',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#0ea5e9',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4 // Smooth curves
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { family: 'Cairo', size: 13 },
                    bodyFont: { family: 'Fira Code', size: 14, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) { return context.parsed.y.toLocaleString('en-US') + ' ر.س'; }
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
                    ticks: { font: { family: 'Cairo', weight: 'bold' }, color: '#64748b' }
                }
            }
        }
    });

    // 2. Packages Distribution Doughnut Chart
    const pkgLabels = <?php echo $data['pkgLabels'] ?? '[]'; ?>;
    const pkgCounts = <?php echo $data['pkgCounts'] ?? '[]'; ?>;
    
    if(pkgCounts.length === 0) {
        pkgLabels.push('لا توجد بيانات');
        pkgCounts.push(1);
    }

    const pkgCtx = document.getElementById('packagesChart').getContext('2d');
    new Chart(pkgCtx, {
        type: 'doughnut',
        data: {
            labels: pkgLabels,
            datasets: [{
                data: pkgCounts,
                backgroundColor: ['#3b82f6', '#f59e0b', '#10b981', '#8b5cf6', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { 
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) { return ' ' + context.parsed + ' مؤسسات'; }
                    }
                }
            }
        }
    });
});
</script>