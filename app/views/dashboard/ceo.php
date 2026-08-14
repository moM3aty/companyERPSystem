<?php
// app/views/dashboard/ceo.php
$stats = $data['stats'] ?? [];
$netProfit = ($stats['revenues'] ?? 0) - ($stats['expenses'] ?? 0);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-black text-dark"><i class="fas fa-crown text-warning me-2"></i> لوحة المالك (CEO Dashboard)</h2>
        <p class="text-muted mt-1" style="font-size: 14px;">نظرة شاملة على الأداء المالي والتشغيلي للمؤسسة.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-dark fw-bold" onclick="location.reload();"><i class="fas fa-sync-alt"></i> تحديث البيانات</button>
    </div>
</div>

<!-- 👑 قسم المؤشرات المالية (KPIs) 👑 -->
<div class="row mb-4">
    <!-- السيولة النقدية -->
    <div class="col-md-3 mb-3">
        <div class="card h-100" style="border-radius: 15px; border: none; box-shadow: 0 10px 20px rgba(0,0,0,0.05); background: linear-gradient(135deg, #1e293b, #0f172a);">
            <div class="card-body p-4 text-white">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="text-slate-300 fw-bold">النقدية المتاحة (سيولة)</div>
                    <div class="p-2 bg-white bg-opacity-10 rounded-circle"><i class="fas fa-vault fs-4 text-info"></i></div>
                </div>
                <h3 class="font-monospace fw-black mb-0 text-white" style="direction: ltr; text-align: left;">
                    <span class="fs-6 text-slate-400 fw-normal">SAR</span> <?php echo number_format($stats['treasury'] ?? 0, 2); ?>
                </h3>
            </div>
        </div>
    </div>

    <!-- صافي الأرباح/التدفق -->
    <div class="col-md-3 mb-3">
        <div class="card h-100" style="border-radius: 15px; border: none; box-shadow: 0 10px 20px rgba(0,0,0,0.05); background: linear-gradient(135deg, #059669, #166534);">
            <div class="card-body p-4 text-white">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="text-emerald-100 fw-bold">صافي التدفق (الربح)</div>
                    <div class="p-2 bg-white bg-opacity-10 rounded-circle"><i class="fas fa-chart-line fs-4 text-white"></i></div>
                </div>
                <h3 class="font-monospace fw-black mb-0 text-white" style="direction: ltr; text-align: left;">
                    <span class="fs-6 text-emerald-200 fw-normal">SAR</span> <?php echo number_format($netProfit, 2); ?>
                </h3>
            </div>
        </div>
    </div>

    <!-- مديونيات العملاء (لنا) -->
    <div class="col-md-3 mb-3">
        <div class="card h-100 bg-white" style="border-radius: 15px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="text-muted fw-bold">ذمم العملاء (أموال لنا)</div>
                    <div class="p-2 bg-blue-50 rounded-circle"><i class="fas fa-hand-holding-usd fs-4 text-primary"></i></div>
                </div>
                <h3 class="font-monospace fw-black mb-0 text-dark" style="direction: ltr; text-align: left;">
                    <span class="fs-6 text-muted fw-normal">SAR</span> <?php echo number_format($stats['receivables'] ?? 0, 2); ?>
                </h3>
            </div>
        </div>
    </div>

    <!-- التزامات الموردين (علينا) -->
    <div class="col-md-3 mb-3">
        <div class="card h-100 bg-white" style="border-radius: 15px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="text-muted fw-bold">مستحقات الموردين (ديون علينا)</div>
                    <div class="p-2 bg-red-50 rounded-circle"><i class="fas fa-file-invoice-dollar fs-4 text-danger"></i></div>
                </div>
                <h3 class="font-monospace fw-black mb-0 text-dark" style="direction: ltr; text-align: left;">
                    <span class="fs-6 text-muted fw-normal">SAR</span> <?php echo number_format($stats['payables'] ?? 0, 2); ?>
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- 📊 قسم الرسوم البيانية والملخصات 📊 -->
<div class="row mb-4">
    <div class="col-md-8 mb-3">
        <div class="card h-100 bg-white" style="border-radius: 15px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold text-dark"><i class="fas fa-balance-scale text-primary me-2"></i> الإيرادات مقابل المصروفات (ملخص)</h5>
            </div>
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                <!-- رسم بياني مبسط كلاسيكي بالـ HTML/CSS بدون مكتبات خارجية لضمان السرعة -->
                <div class="d-flex align-items-end justify-content-center gap-5 w-100" style="height: 250px; border-bottom: 2px solid #cbd5e1; padding-bottom: 10px;">
                    
                    <?php 
                        $max = max((float)$stats['revenues'], (float)$stats['expenses'], 1); // 1 لمنع القسمة على صفر
                        $revHeight = (($stats['revenues'] / $max) * 100) * 0.9; 
                        $expHeight = (($stats['expenses'] / $max) * 100) * 0.9;
                    ?>

                    <!-- عمود الإيرادات -->
                    <div class="d-flex flex-column align-items-center" style="width: 100px;">
                        <span class="font-monospace fw-bold text-success mb-2"><?php echo number_format($stats['revenues']/1000, 1); ?>k</span>
                        <div class="bg-success rounded-top" style="width: 60px; height: <?php echo max($revHeight, 5); ?>%; transition: height 1s ease;"></div>
                        <span class="mt-2 fw-bold text-muted">المقبوضات</span>
                    </div>

                    <!-- عمود المصروفات -->
                    <div class="d-flex flex-column align-items-center" style="width: 100px;">
                        <span class="font-monospace fw-bold text-danger mb-2"><?php echo number_format($stats['expenses']/1000, 1); ?>k</span>
                        <div class="bg-danger rounded-top" style="width: 60px; height: <?php echo max($expHeight, 5); ?>%; transition: height 1s ease;"></div>
                        <span class="mt-2 fw-bold text-muted">المصروفات</span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة (Quick Links/Info) -->
    <div class="col-md-4 mb-3">
        <div class="card h-100 bg-white" style="border-radius: 15px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold text-dark"><i class="fas fa-bolt text-warning me-2"></i> وصول سريع</h5>
            </div>
            <div class="card-body p-4">
                <a href="<?php echo URLROOT; ?>/accounting/dashboard" class="d-flex align-items-center justify-content-between p-3 mb-3 rounded" style="background: #f8fafc; text-decoration: none; color: #334155; border: 1px solid #e2e8f0; transition: 0.2s;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-white rounded shadow-sm text-primary"><i class="fas fa-calculator"></i></div>
                        <span class="fw-bold">اللوحة المالية والتفصيلية</span>
                    </div>
                    <i class="fas fa-chevron-left text-muted"></i>
                </a>

                <a href="<?php echo URLROOT; ?>/hrDashboard/index" class="d-flex align-items-center justify-content-between p-3 mb-3 rounded" style="background: #f8fafc; text-decoration: none; color: #334155; border: 1px solid #e2e8f0; transition: 0.2s;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-white rounded shadow-sm text-success"><i class="fas fa-users-gear"></i></div>
                        <span class="fw-bold">لوحة الموارد البشرية (HR)</span>
                    </div>
                    <i class="fas fa-chevron-left text-muted"></i>
                </a>

                <a href="<?php echo URLROOT; ?>/report/index" class="d-flex align-items-center justify-content-between p-3 rounded" style="background: #f8fafc; text-decoration: none; color: #334155; border: 1px solid #e2e8f0; transition: 0.2s;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-white rounded shadow-sm text-danger"><i class="fas fa-chart-pie"></i></div>
                        <span class="fw-bold">التقارير الذكية</span>
                    </div>
                    <i class="fas fa-chevron-left text-muted"></i>
                </a>
            </div>
        </div>
    </div>
</div>