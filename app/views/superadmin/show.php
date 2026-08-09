<?php
// app/views/superadmin/show.php
$company = $data['company'] ?? null;
$stats = $data['stats'] ?? [];

$userUsage = ($stats['users_count'] / max(1, $company->max_users)) * 100;
$branchUsage = ($stats['branches_count'] / max(1, $company->max_branches)) * 100;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-satellite-dish text-info"></i> مراقبة أداء العميل: <?php echo htmlspecialchars($company->name); ?></h3>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/superadmin/edit/<?php echo $company->id; ?>" class="btn btn-warning"><i class="fas fa-cogs"></i> إدارة الباقة والحدود</a>
        <a href="<?php echo URLROOT; ?>/superadmin/dashboard" class="btn btn-secondary">العودة للوحة</a>
    </div>
</div>

<div class="row d-flex gap-4 mb-4">
    
    <div class="card flex-1 mb-0">
        <div class="card-header bg-light"><h3 class="card-title"><i class="fas fa-battery-half text-primary"></i> استهلاك الموارد (Limits)</h3></div>
        <div class="card-body">
            
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold text-dark">المستخدمين المسجلين (Users)</span>
                    <span class="font-monospace fw-bold"><?php echo $stats['users_count']; ?> / <?php echo $company->max_users; ?></span>
                </div>
                <div class="progress" style="height: 10px; background: var(--slate-200); border-radius: 5px; overflow: hidden;">
                    <div class="progress-bar <?php echo $userUsage > 90 ? 'bg-danger' : 'bg-primary'; ?>" style="width: <?php echo min(100, $userUsage); ?>%;"></div>
                </div>
            </div>

            <div class="mb-2">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold text-dark">الفروع/المستودعات (Branches)</span>
                    <span class="font-monospace fw-bold"><?php echo $stats['branches_count']; ?> / <?php echo $company->max_branches; ?></span>
                </div>
                <div class="progress" style="height: 10px; background: var(--slate-200); border-radius: 5px; overflow: hidden;">
                    <div class="progress-bar <?php echo $branchUsage > 90 ? 'bg-danger' : 'bg-success'; ?>" style="width: <?php echo min(100, $branchUsage); ?>%;"></div>
                </div>
            </div>

        </div>
    </div>

    <div class="card flex-1 mb-0">
        <div class="card-header bg-light"><h3 class="card-title"><i class="fas fa-chart-line text-success"></i> نشاط الشركة (Activity)</h3></div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <span class="text-muted fw-bold">إجمالي المبيعات المحققة للعميل:</span>
                <span class="font-monospace fs-4 fw-bold text-success" style="direction:ltr;"><?php echo number_format($stats['total_sales'], 2); ?> SAR</span>
            </div>
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <span class="text-muted fw-bold">عدد الموظفين (نظام الـ HR):</span>
                <span class="font-monospace fs-4 fw-bold text-primary"><?php echo $stats['employees_count']; ?> موظف</span>
            </div>
            <div class="p-3 text-center">
                <span class="badge badge-secondary fs-6 mt-2">الباقة: <?php echo strtoupper($company->subscription_plan); ?></span>
                <span class="badge badge-danger fs-6 mt-2">التجديد: <?php echo $company->subscription_end; ?></span>
            </div>
        </div>
    </div>

</div>