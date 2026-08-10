<?php
// app/views/exitProcess/index.php
$exits = $data['exits'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-door-open text-danger"></i> الاستقالات وإنهاء الخدمات (Exit Process)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">متابعة إجراءات استقالة الموظفين، إخلاء الطرف، والمخالصات المالية.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/exitProcess/importExcel" method="POST" enctype="multipart/form-data" id="excelForm" style="display:none;">
            <input type="file" id="excelUpload" name="excel_file" accept=".xlsx, .xls, .csv" onchange="document.getElementById('excelForm').submit()">
        </form>
        <button class="btn btn-success" onclick="document.getElementById('excelUpload').click()"><i class="fas fa-file-excel"></i> استيراد</button>
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-file-pdf"></i> PDF</button>
        <a href="<?php echo URLROOT; ?>/exitProcess/create" class="btn btn-danger text-white"><i class="fas fa-user-minus"></i> بدء استقالة</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">تقرير إخلاء الطرف وإنهاء الخدمات</h2>
    <h5 class="text-muted font-monospace">تاريخ: <?php echo date('Y-m-d'); ?></h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف المستقيل</th>
                        <th class="text-center">تاريخ الاستقالة</th>
                        <th class="text-center">آخر يوم عمل</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center d-print-none">اعتماد ومخالصة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exits as $x) : 
                        $statusClass = $x->status === 'completed' ? 'badge-dark' : 'badge-warning';
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="fas fa-user-circle text-muted me-1"></i> <?php echo htmlspecialchars($x->employee_name); ?></td>
                        <td class="text-center font-monospace fs-6"><?php echo $x->resignation_date; ?></td>
                        <td class="text-center font-monospace fs-6 text-danger fw-bold"><?php echo $x->last_working_day; ?></td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $x->status === 'completed' ? 'مخالصة نهائية' : 'قيد الإجراء (مغادر)'; ?></span></td>
                        <td class="text-center d-print-none">
                            <?php if($x->status !== 'completed'): ?>
                                <a href="<?php echo URLROOT; ?>/exitProcess/edit/<?php echo $x->id; ?>" class="btn btn-sm btn-outline-danger fw-bold"><i class="fas fa-check-double"></i> إتمام المخالصة</a>
                            <?php else: ?>
                                <i class="fas fa-lock text-muted"></i>
                            <?php endif; ?>
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