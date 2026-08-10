<?php
// app/views/employee/index.php
$employees = $data['employees'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-users text-primary"></i> دليل الموظفين (Employee Master)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة بيانات موظفيك، عقودهم، والوصول السريع لملفاتهم الشاملة.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-success" onclick="exportToExcel('employees_table')"><i class="fas fa-file-excel"></i> تصدير إكسيل</button>
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-file-pdf"></i> طباعة القائمة</button>
        <a href="<?php echo URLROOT; ?>/employee/create" class="btn btn-primary"><i class="fas fa-user-plus"></i> إضافة موظف جديد</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">دليل الموظفين الشامل</h2>
    <h5 class="text-muted font-monospace">تاريخ التقرير: <?php echo date('Y-m-d'); ?></h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover" id="employees_table">
                <thead class="bg-light">
                    <tr>
                        <th>الرقم الوظيفي</th>
                        <th>الاسم الكامل (Full Name)</th>
                        <th>المنصب (Position)</th>
                        <th>القسم / الإدارة</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center d-print-none">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $e) : 
                        $statusClass = match($e->employment_status) { 'Active' => 'badge-success', 'Exit Process' => 'badge-warning', 'Terminated' => 'badge-danger', default => 'badge-secondary' };
                        $statusLabel = match($e->employment_status) { 'Active' => 'نشط', 'Exit Process' => 'إخلاء طرف', 'Terminated' => 'منهي خدماته', default => $e->employment_status };
                    ?>
                    <tr>
                        <td class="font-monospace text-muted fw-bold"><?php echo htmlspecialchars($e->employee_number); ?></td>
                        <td class="fw-bold text-dark"><i class="fas fa-user-circle text-primary me-1"></i> <?php echo htmlspecialchars($e->full_name); ?></td>
                        <td><?php echo htmlspecialchars($e->position ?: $e->job_title ?: '—'); ?></td>
                        <td><?php echo htmlspecialchars($e->department_id ?? '—'); ?></td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td class="text-center d-print-none">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/employee/show/<?php echo $e->id; ?>" class="btn-icon view text-primary" title="الملف الشامل"><i class="fas fa-id-card"></i></a>
                                <a href="<?php echo URLROOT; ?>/employee/edit/<?php echo $e->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/employee/delete/<?php echo $e->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد مسح ملف الموظف نهائياً؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; if(empty($employees)): ?>
                        <tr><td colspan="6" class="text-center text-muted p-5">لا يوجد موظفين مسجلين.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function exportToExcel(tableId) {
    let html = document.getElementById(tableId).outerHTML;
    let url = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent('<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="utf-8"></head><body>' + html + '</body></html>');
    let downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    downloadLink.href = url;
    downloadLink.download = 'Employees_Report_' + new Date().toISOString().slice(0,10) + '.xls';
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>

<style>
@media print { body { background: #fff !important; } .d-print-none, .sidebar, .topbar { display: none !important; } .main-content { margin: 0 !important; } .card { border: 1px solid #000 !important; box-shadow: none !important; } }
</style>