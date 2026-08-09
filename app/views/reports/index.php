<?php
// app/views/reports/index.php
?>

<div class="d-flex justify-content-end align-items-center mb-5 mt-2">
    <div class="text-end">
        <h3 class="mb-0 text-dark fw-black"><i class="fas fa-chart-pie text-primary ms-2"></i> التقارير الذكية (Smart Reports)</h3>
        <p class="text-muted mt-2" style="font-size: 14px;">اختر التقرير المطلوب لاستعراض الأداء المالي والتشغيلي لشركتك.</p>
    </div>
</div>

<div class="reports-grid">
    
    <!-- 1. الموارد البشرية (HR) -->
    <a href="<?php echo URLROOT; ?>/report/hr" class="report-card text-decoration-none">
        <div class="rc-body">
            <div class="rc-icon-wrapper" style="background-color: #f1f5f9; color: #64748b;">
                <i class="fas fa-users-gear"></i>
            </div>
            <h4 class="rc-title">الموارد البشرية (HR)</h4>
            <p class="rc-desc">تقارير الرواتب، الحضور والانصراف، والمكافآت.</p>
        </div>
    </a>

    <!-- 2. المشتريات والموردين -->
    <a href="<?php echo URLROOT; ?>/report/purchases" class="report-card text-decoration-none">
        <div class="rc-body">
            <div class="rc-icon-wrapper" style="background-color: #eff6ff; color: #3b82f6;">
                <i class="fas fa-boxes-packing"></i>
            </div>
            <h4 class="rc-title">المشتريات والموردين</h4>
            <p class="rc-desc">تتبع أوامر الشراء، المرتجعات، وإجمالي المدفوعات للموردين.</p>
        </div>
        <div class="rc-footer" style="background-color: #3b82f6;"></div>
    </a>

    <!-- 3. المبيعات والضرائب -->
    <div class="report-card active-card">
        <div class="rc-body">
            <div class="rc-icon-wrapper" style="background-color: #d1fae5; color: #10b981;">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <h4 class="rc-title">المبيعات والضرائب</h4>
            <p class="rc-desc">تحليل فواتير المبيعات، المنتجات الأكثر مبيعاً، وملخص ضريبة القيمة المضافة للفترات.</p>
            
            <a href="<?php echo URLROOT; ?>/report/sales" class="btn btn-success w-100 mt-3 d-flex align-items-center justify-content-center gap-2" style="padding: 12px; font-size: 15px; border-radius: 8px;">
                <i class="fas fa-chart-line"></i> استخراج التقرير
            </a>
        </div>
        <div class="rc-footer" style="background-color: #10b981;"></div>
    </div>

</div>

<style>
    /* CSS مخصص لشاشة التقارير لمطابقة الصورة تماماً */
    .reports-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-top: 20px;
        direction: rtl;
    }
    
    @media (max-width: 992px) {
        .reports-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .reports-grid { grid-template-columns: 1fr; }
    }

    .report-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 280px;
    }

    /* تأثير الـ Hover للكروت الغير نشطة */
    a.report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }

    /* الكارت الذي يحتوي على حواف متقطعة (كما في الصورة) */
    a.report-card:first-child {
        border: 2px dashed #cbd5e1;
        background: transparent;
    }
    a.report-card:first-child:hover {
        border-color: #94a3b8;
        background: #ffffff;
    }

    /* كارت المبيعات (النشط في الصورة) */
    .active-card {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .rc-body {
        padding: 40px 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex: 1;
    }

    .rc-icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 25px;
    }

    .rc-title {
        font-size: 18px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 12px;
    }

    .rc-desc {
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
        margin: 0;
    }

    /* الخط السفلي الملون للكروت */
    .rc-footer {
        height: 4px;
        width: 100%;
        position: absolute;
        bottom: 0;
        left: 0;
    }
</style>