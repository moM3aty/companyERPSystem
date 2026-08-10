<?php
// app/views/sanction/index.php
$sanctions = $data['sanctions'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-scale-balanced text-danger"></i> الجزاءات والقرارات الإدارية</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">توجيه الإنذارات وتسجيل الخصومات ليتم خصمها آلياً من مسير الرواتب.</p>
    </div>
    
    <!-- 🟢 شريط أدوات التصدير والاستيراد الموحد 🟢 -->
    <div class="d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/sanction/importExcel" method="POST" enctype="multipart/form-data" id="excelForm" style="display:none;">
            <input type="file" id="excelUpload" name="excel_file" accept=".xlsx, .xls, .csv" onchange="document.getElementById('excelForm').submit()">
        </form>
        
        <button class="btn btn-success" onclick="document.getElementById('excelUpload').click()">
            <i class="fas fa-file-excel"></i> استيراد إكسيل
        </button>
        
        <button class="btn btn-dark" onclick="window.print()">
            <i class="fas fa-file-pdf"></i> تصدير PDF / طباعة
        </button>

        <a href="<?php echo URLROOT; ?>/sanction/create" class="btn btn-danger text-white">
            <i class="fas fa-plus"></i> إصدار قرار (إنذار / خصم)
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

<!-- 🟢 ترويسة تظهر في الطباعة/PDF فقط 🟢 -->
<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">سجل القرارات الإدارية والجزاءات</h2>
    <h5 class="text-muted font-monospace">تاريخ التقرير: <?php echo date('Y-m-d'); ?></h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف</th>
                        <th class="text-center">التاريخ</th>
                        <th class="text-center">نوع القرار</th>
                        <th class="text-left">قيمة الخصم (ر.س)</th>
                        <th>السبب / المبررات</th>
                        <th class="text-center d-print-none">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sanctions as $s) : ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="fas fa-user text-muted me-1"></i> <?php echo htmlspecialchars($s->employee_name); ?></td>
                        <td class="text-center text-muted font-monospace fs-6"><?php echo date('Y-m-d', strtotime($s->date)); ?></td>
                        <td class="text-center">
                            <?php if($s->type === 'deduction'): ?>
                                <span class="badge badge-danger"><i class="fas fa-minus-circle"></i> خصم مالي</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><i class="fas fa-triangle-exclamation"></i> إنذار كتابي</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-left font-monospace fw-bold text-danger fs-5" style="direction:ltr;">
                            <?php echo $s->type === 'deduction' ? number_format($s->amount, 2) : '—'; ?>
                        </td>
                        <td class="text-muted" style="font-size:13px; max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo htmlspecialchars($s->reason); ?>">
                            <?php echo htmlspecialchars($s->reason); ?>
                            <div style="font-size:10px; color:#94a3b8; margin-top:4px;">بواسطة: <?php echo htmlspecialchars($s->admin_name); ?></div>
                        </td>
                        <td class="text-center d-print-none">
                            <?php if(Session::hasRole('admin')): ?>
                            <form action="<?php echo URLROOT; ?>/sanction/delete/<?php echo $s->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('إلغاء القرار الإداري وسحبه من السجل؟');">
                                <button type="submit" class="btn-icon delete" title="سحب القرار"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($sanctions)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-scale-unbalanced fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد جزاءات أو إنذارات مسجلة للموظفين.
                        </td>
                    </tr>
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