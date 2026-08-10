<?php
// app/views/training/index.php
$trainings = $data['trainings'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-graduation-cap text-info"></i> التدريب والتطوير (Training & Development)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع الدورات التدريبية، التكاليف، والمهارات المكتسبة للموظفين.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/training/importExcel" method="POST" enctype="multipart/form-data" id="excelForm" style="display:none;">
            <input type="file" id="excelUpload" name="excel_file" accept=".xlsx, .xls, .csv" onchange="document.getElementById('excelForm').submit()">
        </form>
        <button class="btn btn-success" onclick="document.getElementById('excelUpload').click()"><i class="fas fa-file-excel"></i> استيراد إكسيل</button>
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-file-pdf"></i> طباعة / PDF</button>
        <a href="<?php echo URLROOT; ?>/training/create" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة دورة تدريبية</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">تقرير التدريب والتطوير للموظفين</h2>
    <h5 class="text-muted font-monospace">تاريخ التقرير: <?php echo date('Y-m-d'); ?></h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف</th>
                        <th>اسم الدورة (Course)</th>
                        <th>جهة التدريب</th>
                        <th class="text-center">التاريخ</th>
                        <th class="text-left">التكلفة (ر.س)</th>
                        <th class="text-center">التقييم</th>
                        <th class="text-center d-print-none">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trainings as $t) : 
                        $evalClass = match($t->evaluation) { 'excellent' => 'badge-success', 'good' => 'badge-primary', 'poor' => 'badge-danger', default => 'badge-warning' };
                        $evalLabel = match($t->evaluation) { 'excellent' => 'ممتاز', 'good' => 'جيد', 'poor' => 'ضعيف', default => 'قيد الانتظار' };
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="fas fa-user-tie text-muted me-1"></i> <?php echo htmlspecialchars($t->employee_name); ?></td>
                        <td class="fw-bold text-info"><?php echo htmlspecialchars($t->course_name); ?></td>
                        <td><?php echo htmlspecialchars($t->provider ?? '—'); ?></td>
                        <td class="text-center font-monospace fs-6 text-muted"><?php echo $t->course_date; ?></td>
                        <td class="text-left font-monospace fw-bold text-danger fs-5" style="direction:ltr;"><?php echo number_format($t->cost, 2); ?></td>
                        <td class="text-center"><span class="badge <?php echo $evalClass; ?>"><?php echo $evalLabel; ?></span></td>
                        <td class="text-center d-print-none">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/training/edit/<?php echo $t->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/training/delete/<?php echo $t->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد الحذف؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
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
@media print { body { background: #fff !important; } .d-print-none, .sidebar, .topbar { display: none !important; } .main-content { margin: 0 !important; } .card { border: 1px solid #000 !important; box-shadow: none !important; page-break-inside: avoid; } .d-print-block { display: block !important; } }
</style>