<?php
// app/views/reports/index.php
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-chart-pie text-primary"></i> التقارير الذكية والمحاسبية (Financial Reports)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">اختر التقرير المطلوب لاستعراض الأداء المالي والتشغيلي لشركتك.</p>
    </div>
</div>

<h5 class="fw-bold text-dark border-bottom pb-2 mb-4"><i class="fas fa-calculator text-muted"></i> التقارير المالية الأساسية</h5>
<div class="form-grid mb-5" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
    
    <!-- قائمة الدخل -->
    <div class="card mb-0 hover-card shadow-sm" style="cursor: pointer; border-bottom: 4px solid var(--primary);" onclick="window.location.href='<?php echo URLROOT; ?>/report/incomeStatement'">
        <div class="card-body text-center p-4">
            <div style="width: 70px; height: 70px; background: var(--primary-light); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 15px;">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">قائمة الدخل (P&L)</h5>
            <p class="text-muted mb-4" style="font-size: 12px;">تقرير الإيرادات والمصروفات وصافي الأرباح (Profit and Loss).</p>
            <a href="<?php echo URLROOT; ?>/report/incomeStatement" class="btn btn-primary btn-sm w-100"><i class="fas fa-eye"></i> استخراج التقرير</a>
        </div>
    </div>

    <!-- الميزانية العمومية -->
    <div class="card mb-0 hover-card shadow-sm" style="cursor: pointer; border-bottom: 4px solid var(--warning);" onclick="window.location.href='<?php echo URLROOT; ?>/report/balanceSheet'">
        <div class="card-body text-center p-4">
            <div style="width: 70px; height: 70px; background: var(--warning-light); color: var(--warning); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 15px;">
                <i class="fas fa-scale-balanced"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">الميزانية العمومية</h5>
            <p class="text-muted mb-4" style="font-size: 12px;">تقرير الأصول، الخصوم، وحقوق الملكية (Balance Sheet).</p>
            <a href="<?php echo URLROOT; ?>/report/balanceSheet" class="btn btn-warning btn-sm w-100 text-white"><i class="fas fa-eye"></i> استخراج التقرير</a>
        </div>
    </div>
</div>

<h5 class="fw-bold text-dark border-bottom pb-2 mb-4"><i class="fas fa-briefcase text-muted"></i> التقارير التشغيلية</h5>
<div class="form-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
    
    <!-- تقرير الموارد البشرية -->
    <div class="card mb-0 hover-card shadow-sm" style="cursor: pointer; border-bottom: 4px solid var(--secondary);" onclick="window.location.href='<?php echo URLROOT; ?>/report/hr'">
        <div class="card-body text-center p-4">
            <div style="width: 70px; height: 70px; background: var(--slate-200); color: var(--slate-600); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 15px;">
                <i class="fas fa-users-gear"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">الموارد البشرية (HR)</h5>
            <p class="text-muted mb-4" style="font-size: 12px;">تقارير الرواتب، الحضور والانصراف، والمكافآت.</p>
            <a href="<?php echo URLROOT; ?>/report/hr" class="btn btn-secondary btn-sm w-100"><i class="fas fa-eye"></i> استخراج التقرير</a>
        </div>
    </div>

    <!-- تقرير المشتريات -->
    <div class="card mb-0 hover-card shadow-sm" style="cursor: pointer; border-bottom: 4px solid var(--info);" onclick="window.location.href='<?php echo URLROOT; ?>/report/purchases'">
        <div class="card-body text-center p-4">
            <div style="width: 70px; height: 70px; background: var(--info-light); color: var(--info); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 15px;">
                <i class="fas fa-boxes-packing"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">المشتريات والموردين</h5>
            <p class="text-muted mb-4" style="font-size: 12px;">تتبع أوامر الشراء، المرتجعات، وإجمالي المدفوعات.</p>
            <a href="<?php echo URLROOT; ?>/report/purchases" class="btn btn-info btn-sm text-white w-100"><i class="fas fa-eye"></i> استخراج التقرير</a>
        </div>
    </div>

    <!-- تقرير المبيعات والضرائب -->
    <div class="card mb-0 hover-card shadow-sm" style="cursor: pointer; border-bottom: 4px solid var(--success);" onclick="window.location.href='<?php echo URLROOT; ?>/report/sales'">
        <div class="card-body text-center p-4">
            <div style="width: 70px; height: 70px; background: var(--success-light); color: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 15px;">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">المبيعات والضرائب</h5>
            <p class="text-muted mb-4" style="font-size: 12px;">تحليل فواتير المبيعات، المنتجات الأكثر مبيعاً، والضريبة.</p>
            <a href="<?php echo URLROOT; ?>/report/sales" class="btn btn-success btn-sm w-100"><i class="fas fa-eye"></i> استخراج التقرير</a>
        </div>
    </div>

</div>

<style>
.hover-card { transition: all 0.3s ease; border: 1px solid var(--border-color); }
.hover-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); border-color: var(--primary); }
</style>