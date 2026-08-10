<?php
// app/views/employeeContract/index.php
$contracts = $data['contracts'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-signature text-primary"></i> عقود الموظفين (Employment Contracts)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة تواريخ صلاحية العقود، الرواتب، والبدلات للتأكد من الامتثال.</p>
    </div>
    
    <div class="d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/employeeContract/importExcel" method="POST" enctype="multipart/form-data" id="excelForm" style="display:none;">
            <input type="file" id="excelUpload" name="excel_file" accept=".xlsx, .xls, .csv" onchange="document.getElementById('excelForm').submit()">
        </form>
        
        <button class="btn btn-success" onclick="document.getElementById('excelUpload').click()">
            <i class="fas fa-file-excel"></i> استيراد
        </button>
        <button class="btn btn-dark" onclick="window.print()">
            <i class="fas fa-file-pdf"></i> تصدير PDF
        </button>
        <a href="<?php echo URLROOT; ?>/employeeContract/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> تسجيل عقد جديد
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
    <h2 class="fw-black text-dark mb-1">سجل عقود الموظفين</h2>
    <h5 class="text-muted font-monospace">تاريخ التقرير: <?php echo date('Y-m-d'); ?></h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف</th>
                        <th class="text-center">تاريخ البداية</th>
                        <th class="text-center">تاريخ الانتهاء (التنبيه)</th>
                        <th class="text-left">الراتب الأساسي</th>
                        <th class="text-left">البدلات</th>
                        <th class="text-center d-print-none">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contracts as $c) : 
                        $daysLeft = null;
                        $alertClass = 'text-muted';
                        $alertText = '';
                        
                        if (!empty($c->end_date)) {
                            $daysLeft = ceil((strtotime($c->end_date) - time()) / 86400);
                            if ($daysLeft < 0 || $c->status === 'expired') {
                                $alertClass = 'badge-danger';
                                $alertText = 'منتهي';
                            } elseif ($daysLeft <= 30) {
                                $alertClass = 'badge-warning';
                                $alertText = 'ينتهي خلال '.$daysLeft.' يوم';
                            } else {
                                $alertClass = 'badge-success';
                                $alertText = 'ساري';
                            }
                        } else {
                            $alertClass = 'badge-info';
                            $alertText = 'مفتوح (غير محدد)';
                        }
                    ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark"><i class="fas fa-user-tie text-muted me-1"></i> <?php echo htmlspecialchars($c->employee_name); ?></div>
                            <div class="text-muted font-monospace" style="font-size:11px;">ID: <?php echo htmlspecialchars($c->employee_number ?? '-'); ?></div>
                        </td>
                        <td class="text-center text-muted font-monospace fs-6"><?php echo date('Y-m-d', strtotime($c->start_date)); ?></td>
                        <td class="text-center">
                            <div class="font-monospace fw-bold mb-1"><?php echo !empty($c->end_date) ? date('Y-m-d', strtotime($c->end_date)) : '—'; ?></div>
                            <span class="badge <?php echo $alertClass; ?> fs-7"><?php echo $alertText; ?></span>
                        </td>
                        <td class="text-left font-monospace fw-bold text-primary fs-5" style="direction:ltr;">
                            <?php echo number_format($c->basic_salary, 2); ?>
                        </td>
                        <td class="text-left font-monospace fw-bold text-success fs-5" style="direction:ltr;">
                            <?php echo number_format($c->allowances, 2); ?>
                        </td>
                        <td class="text-center d-print-none">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/employeeContract/edit/<?php echo $c->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/employeeContract/delete/<?php echo $c->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف العقد؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($contracts)) : ?>
                    <tr><td colspan="6" class="text-center text-muted p-5"><i class="fas fa-file-signature fs-1 mb-3 opacity-50 d-block"></i> لا توجد عقود موظفين مسجلة.</td></tr>
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