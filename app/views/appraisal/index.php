<?php
// app/views/appraisal/index.php
$appraisals = $data['appraisals'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-star-half-stroke text-warning"></i> تقييم الأداء (Performance Management)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">سجل تقييمات الموظفين الدورية لتحديد المكافآت والترقيات بإنصاف.</p>
    </div>
    
    <!-- 🟢 Toolbar 🟢 -->
    <div class="d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/appraisal/importExcel" method="POST" enctype="multipart/form-data" id="excelForm" style="display:none;">
            <input type="file" id="excelUpload" name="excel_file" accept=".xlsx, .xls, .csv" onchange="document.getElementById('excelForm').submit()">
        </form>
        <button class="btn btn-success" onclick="document.getElementById('excelUpload').click()">
            <i class="fas fa-file-excel"></i> استيراد
        </button>
        <button class="btn btn-dark" onclick="window.print()">
            <i class="fas fa-file-pdf"></i> طباعة / PDF
        </button>
        <a href="<?php echo URLROOT; ?>/appraisal/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> تقييم جديد
        </a>
    </div>
</div>

<?php 
    $flash = Session::getFlash(); 
    if ($flash): 
?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>">
        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i> 
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>

<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">تقرير تقييمات أداء الموظفين</h2>
    <h5 class="text-muted font-monospace">تاريخ التقرير: <?php echo date('Y-m-d'); ?></h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف</th>
                        <th class="text-center">تاريخ التقييم</th>
                        <th class="text-center">الإنجاز (KPI)</th>
                        <th class="text-center">السلوك</th>
                        <th class="text-center">الحضور</th>
                        <th class="text-center">النتيجة النهائية</th>
                        <th class="text-center">التقدير</th>
                        <th class="text-center d-print-none">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appraisals as $a) : 
                        $gradeClass = match($a->grade) {
                            'ممتاز' => 'badge-success', 
                            'جيد جداً' => 'badge-primary', 
                            'جيد' => 'badge-warning', 
                            default => 'badge-danger'
                        };
                    ?>
                    <tr>
                        <td class="fw-bold text-dark">
                            <i class="fas fa-user-tie text-muted me-1"></i> <?php echo htmlspecialchars($a->employee_name); ?>
                            <div style="font-size:10px; color:var(--slate-400); font-weight:normal; margin-top:2px;">بواسطة: <?php echo htmlspecialchars($a->evaluator_name ?? 'المدير'); ?></div>
                        </td>
                        <td class="text-center font-monospace text-muted fs-6"><?php echo $a->evaluation_date; ?></td>
                        <td class="text-center font-monospace"><?php echo $a->performance_score; ?>%</td>
                        <td class="text-center font-monospace"><?php echo $a->behavior_score; ?>%</td>
                        <td class="text-center font-monospace"><?php echo $a->attendance_score; ?>%</td>
                        <td class="text-center font-monospace fw-bold text-info fs-5"><?php echo $a->total_score; ?>%</td>
                        <td class="text-center"><span class="badge <?php echo $gradeClass; ?>"><?php echo $a->grade; ?></span></td>
                        <td class="text-center d-print-none">
                            <?php if(Session::hasRole('admin') || Session::hasRole('manager')): ?>
                                <form action="<?php echo URLROOT; ?>/appraisal/delete/<?php echo $a->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد مسح هذا التقييم؟');">
                                    <button type="submit" class="btn-icon delete" title="مسح التقييم"><i class="fas fa-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if(empty($appraisals)): ?>
                    <tr><td colspan="8" class="text-center text-muted p-5"><i class="fas fa-star-half-stroke fs-1 mb-3 opacity-50 d-block"></i> لا توجد تقييمات مسجلة حالياً.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: #fff !important; }
    .d-print-none, .sidebar, .topbar { display: none !important; }
    .main-content { margin: 0 !important; }
    .card { box-shadow: none !important; border: 1px solid var(--border-color) !important; page-break-inside: avoid; }
    .d-print-block { display: block !important; }
}
</style>