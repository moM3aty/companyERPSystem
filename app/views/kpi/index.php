<?php
// app/views/kpi/index.php
$kpis = $data['kpis'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-bullseye text-primary"></i> مؤشرات الأداء والتقييم (KPIs & Appraisals)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع تحقيق الأهداف الربع سنوية والسنوية للموظفين بخوارزمية (Target vs Actual).</p>
    </div>
    <div class="d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/kpi/importExcel" method="POST" enctype="multipart/form-data" id="excelForm" style="display:none;">
            <input type="file" id="excelUpload" name="excel_file" accept=".xlsx, .xls, .csv" onchange="document.getElementById('excelForm').submit()">
        </form>
        <button class="btn btn-success" onclick="document.getElementById('excelUpload').click()"><i class="fas fa-file-excel"></i> استيراد</button>
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-file-pdf"></i> PDF</button>
        <a href="<?php echo URLROOT; ?>/kpi/create" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة تقييم جديد</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">تقرير مؤشرات قياس الأداء (KPIs)</h2>
    <h5 class="text-muted font-monospace">تاريخ التقرير: <?php echo date('Y-m-d'); ?></h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف</th>
                        <th>اسم المؤشر (KPI Name)</th>
                        <th class="text-center">الهدف (Target)</th>
                        <th class="text-center">النتيجة (Actual)</th>
                        <th class="text-center">الإنجاز (%)</th>
                        <th class="text-center">الوزن (Weight)</th>
                        <th class="text-center d-print-none">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kpis as $k) : 
                        $achieve = (float)$k->achievement_percent;
                        $colorClass = $achieve >= 100 ? 'text-success' : ($achieve >= 75 ? 'text-primary' : 'text-danger');
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="fas fa-user-tie text-muted me-1"></i> <?php echo htmlspecialchars($k->employee_name); ?></td>
                        <td class="fw-bold text-info"><?php echo htmlspecialchars($k->kpi_name); ?> <br><small class="text-muted font-monospace"><?php echo htmlspecialchars($k->review_period); ?></small></td>
                        <td class="text-center font-monospace fs-5 text-muted"><?php echo number_format($k->target_value); ?></td>
                        <td class="text-center font-monospace fs-5 text-dark fw-bold"><?php echo number_format($k->actual_value); ?></td>
                        <td class="text-center font-monospace fs-4 fw-black <?php echo $colorClass; ?>" style="direction:ltr;"><?php echo $k->achievement_percent; ?>%</td>
                        <td class="text-center font-monospace"><span class="badge badge-secondary fs-6"><?php echo $k->weight; ?>%</span></td>
                        <td class="text-center d-print-none">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/kpi/edit/<?php echo $k->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/kpi/delete/<?php echo $k->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد مسح التقييم؟');">
                                    <button type="submit" class="btn-icon delete"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print { body { background: #fff !important; } .d-print-none, .sidebar, .topbar { display: none !important; } .main-content { margin: 0 !important; } .card { border: 1px solid #000 !important; box-shadow: none !important; } .d-print-block { display: block !important; } }
</style>