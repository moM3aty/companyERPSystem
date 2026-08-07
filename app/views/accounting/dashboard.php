<?php
// المسار: app/views/accounting/dashboard.php
$stats = $data['stats'] ?? [];
$recentEntries = $data['recent_entries'] ?? [];
?>

<div class="card" style="background: linear-gradient(135deg, var(--primary) 0%, #0891b2 100%); color: #fff; border: none; overflow: hidden; position: relative;">
    <i class="fas fa-scale-balanced" style="position: absolute; left: -20px; top: -30px; font-size: 150px; opacity: 0.1;"></i>
    <div class="card-body" style="position: relative; z-index: 2; padding: 40px;">
        <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 8px;">لوحة التحكم المالية</h1>
        <p style="font-size: 15px; opacity: 0.9; max-width: 600px; margin: 0;">نظرة عامة على الأرصدة، الحركات المحاسبية، والتقارير المالية الأساسية لدعم اتخاذ القرار.</p>
    </div>
</div>

<div class="form-grid mb-4" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 56px; height: 56px; background: var(--success-light); color: var(--success); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <h4 class="font-monospace mb-1" style="font-size: 24px; font-weight: 800; color: var(--text-dark);"><?php echo number_format($stats['total_assets'] ?? 0, 2); ?></h4>
                <span class="text-muted" style="font-size: 13px; font-weight: 700;">إجمالي الأصول (ر.س)</span>
            </div>
        </div>
    </div>
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 56px; height: 56px; background: var(--danger-light); color: var(--danger); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <div>
                <h4 class="font-monospace mb-1" style="font-size: 24px; font-weight: 800; color: var(--text-dark);"><?php echo number_format($stats['total_liabilities'] ?? 0, 2); ?></h4>
                <span class="text-muted" style="font-size: 13px; font-weight: 700;">إجمالي الخصوم والالتزامات</span>
            </div>
        </div>
    </div>
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 56px; height: 56px; background: var(--info-light); color: var(--info); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-money-bill-trend-up"></i>
            </div>
            <div>
                <h4 class="font-monospace mb-1" style="font-size: 24px; font-weight: 800; color: var(--info);"><?php echo number_format($stats['net_income'] ?? 0, 2); ?></h4>
                <span class="text-muted" style="font-size: 13px; font-weight: 700;">صافي الدخل (تقريبي)</span>
            </div>
        </div>
    </div>
</div>

<div class="form-grid" style="grid-template-columns: 2fr 1fr;">
    <div class="card mb-0">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-book-journal-whills text-primary"></i> أحدث القيود اليومية</h3>
            <a href="<?php echo URLROOT; ?>/journal/index" style="font-size: 13px; font-weight: 600;">عرض الكل</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>رقم القيد</th>
                            <th>التاريخ</th>
                            <th>البيان</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentEntries)): foreach($recentEntries as $entry): ?>
                        <tr>
                            <td><span class="badge badge-primary font-monospace"><?php echo htmlspecialchars($entry->entry_number); ?></span></td>
                            <td class="text-muted fs-6"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($entry->entry_date)); ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($entry->description); ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="3" class="text-center text-muted p-4">لا توجد قيود مسجلة مؤخراً.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-0">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-bolt text-accent"></i> عمليات محاسبية</h3>
        </div>
        <div class="card-body p-3">
            <div class="d-flex flex-column gap-2">
                <a href="<?php echo URLROOT; ?>/journal/create" class="btn btn-secondary w-100 justify-content-start" style="border-radius: var(--radius-sm); padding: 12px 16px;">
                    <i class="fas fa-plus text-primary"></i> إنشاء قيد يومية مزدوج
                </a>
                <a href="<?php echo URLROOT; ?>/account/tree" class="btn btn-secondary w-100 justify-content-start" style="border-radius: var(--radius-sm); padding: 12px 16px;">
                    <i class="fas fa-sitemap text-primary"></i> استعراض دليل الحسابات
                </a>
                <a href="<?php echo URLROOT; ?>/accounting/incomeStatement" class="btn btn-secondary w-100 justify-content-start" style="border-radius: var(--radius-sm); padding: 12px 16px;">
                    <i class="fas fa-file-invoice-dollar text-primary"></i> قائمة الدخل
                </a>
                <a href="<?php echo URLROOT; ?>/accounting/trialBalance" class="btn btn-secondary w-100 justify-content-start" style="border-radius: var(--radius-sm); padding: 12px 16px;">
                    <i class="fas fa-scale-unbalanced text-primary"></i> ميزان المراجعة
                </a>
                <a href="<?php echo URLROOT; ?>/accounting/balanceSheet" class="btn btn-secondary w-100 justify-content-start" style="border-radius: var(--radius-sm); padding: 12px 16px;">
                    <i class="fas fa-building-columns text-primary"></i> الميزانية العمومية
                </a>
            </div>
        </div>
    </div>
</div>