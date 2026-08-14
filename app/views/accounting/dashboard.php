<?php
// app/views/accounting/dashboard.php
$stats = $data['stats'] ?? ['total_assets'=>0, 'total_liabilities'=>0, 'net_income'=>0];
$recent_entries = $data['recent_entries'] ?? [];
$m = $data['metrics'] ?? [];
$cf = $data['cashFlow'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 text-dark fw-black"><i class="fas fa-chart-line text-primary"></i> الإدارة المالية والمحاسبة</h2>
        <p class="text-muted mt-1" style="font-size: 14px;">مراقبة السيولة، الذمم، والعمليات المحاسبية (شامل الميزانيات).</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="location.reload()"><i class="fas fa-sync-alt"></i> تحديث</button>
    </div>
</div>

<!-- 1. السيولة والبنوك (Liquidity) - من الإضافة الجديدة -->
<h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-vault text-success"></i> السيولة والموقف المالي السريع</h5>
<div class="form-grid mb-4" style="grid-template-columns: repeat(4, 1fr);">
    <div class="card bg-success-light border-success mb-0 shadow-sm">
        <div class="card-body p-3 text-center">
            <i class="fas fa-money-bill-wave text-success fs-2 mb-2"></i>
            <h6 class="text-success-dark fw-bold mb-1">الرصيد النقدي (Cash)</h6>
            <h3 class="font-monospace fw-black text-success m-0" style="direction:ltr;"><?php echo number_format($m['cash_balance'] ?? 0, 2); ?></h3>
        </div>
    </div>
    <div class="card bg-info-light border-info mb-0 shadow-sm">
        <div class="card-body p-3 text-center">
            <i class="fas fa-building-columns text-info fs-2 mb-2"></i>
            <h6 class="text-info-dark fw-bold mb-1">أرصدة البنوك (Banks)</h6>
            <h3 class="font-monospace fw-black text-info m-0" style="direction:ltr;"><?php echo number_format($m['bank_balance'] ?? 0, 2); ?></h3>
        </div>
    </div>
    <div class="card bg-primary-light border-primary mb-0 shadow-sm">
        <div class="card-body p-3 text-center">
            <i class="fas fa-hand-holding-dollar text-primary fs-2 mb-2"></i>
            <h6 class="text-primary-dark fw-bold mb-1">حسابات القبض (AR)</h6>
            <h3 class="font-monospace fw-black text-primary m-0" style="direction:ltr;"><?php echo number_format($m['accounts_receivable'] ?? 0, 2); ?></h3>
        </div>
    </div>
    <div class="card bg-danger-light border-danger mb-0 shadow-sm">
        <div class="card-body p-3 text-center">
            <i class="fas fa-file-invoice-dollar text-danger fs-2 mb-2"></i>
            <h6 class="text-danger-dark fw-bold mb-1">حسابات الدفع (AP)</h6>
            <h3 class="font-monospace fw-black text-danger m-0" style="direction:ltr;"><?php echo number_format($m['accounts_payable'] ?? 0, 2); ?></h3>
        </div>
    </div>
</div>

<!-- 2. أرصدة الميزانية (من كودك القديم) -->
<h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-scale-balanced text-warning"></i> أرصدة الميزانية (الميزان العام)</h5>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 bg-white">
            <div class="card-body text-center">
                <div class="text-muted fw-bold mb-2">إجمالي الأصول (Assets)</div>
                <h3 class="font-monospace fw-black text-primary" style="direction:ltr;"><?php echo number_format($stats['total_assets'], 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 bg-white">
            <div class="card-body text-center">
                <div class="text-muted fw-bold mb-2">إجمالي الخصوم (Liabilities)</div>
                <h3 class="font-monospace fw-black text-danger" style="direction:ltr;"><?php echo number_format($stats['total_liabilities'], 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 bg-dark text-white">
            <div class="card-body text-center">
                <div class="text-white-50 fw-bold mb-2">صافي الدخل من الشجرة (Net Income)</div>
                <h3 class="font-monospace fw-black <?php echo $stats['net_income'] >= 0 ? 'text-success' : 'text-danger'; ?>" style="direction:ltr;">
                    <?php echo number_format($stats['net_income'], 2); ?>
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- 3. رسم التدفق النقدي + أحدث القيود -->
<div class="row">
    <!-- رسم التدفق النقدي -->
    <div class="col-md-7 mb-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header bg-white fw-bold text-primary border-bottom-0"><i class="fas fa-chart-bar"></i> التدفقات النقدية (آخر 6 أشهر)</div>
            <div class="card-body" style="position: relative; height:300px;">
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>
    </div>

    <!-- أحدث القيود (من كودك القديم) -->
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header bg-white fw-bold text-dark border-bottom-0"><i class="fas fa-book-journal-whills"></i> أحدث القيود اليومية</div>
            <ul class="list-group list-group-flush font-monospace">
                <?php foreach($recent_entries as $entry): ?>
                <li class="list-group-item px-3 py-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-primary"><?php echo htmlspecialchars($entry->journal_number ?? $entry->id); ?></strong>
                        <span class="text-muted" style="font-size:11px;"><?php echo date('Y-m-d', strtotime($entry->created_at)); ?></span>
                    </div>
                    <div class="text-dark fw-bold" style="font-size:13px;"><?php echo htmlspecialchars($entry->description); ?></div>
                </li>
                <?php endforeach; if(empty($recent_entries)): ?>
                    <li class="list-group-item text-center text-muted p-4">لا توجد قيود مسجلة.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<!-- تحميل مكتبة Chart.js للرسوم البيانية -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cfData = <?php echo $cf; ?>;
    if(cfData && cfData.labels.length > 0) {
        const ctx = document.getElementById('cashFlowChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: cfData.labels,
                datasets: [
                    {
                        label: 'مقبوضات (Cash In)',
                        data: cfData.in,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderRadius: 4
                    },
                    {
                        label: 'مدفوعات (Cash Out)',
                        data: cfData.out,
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } },
                plugins: {
                    legend: { position: 'top', labels: { font: { family: 'Cairo' } } }
                }
            }
        });
    }
});
</script>