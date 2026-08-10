<?php
// app/views/employeeRequest/index.php
$requests =$data['requests'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-hand-paper text-primary"></i> طلبات الموظفين (Self-Service)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تلقي وإدارة طلبات الموظفين المختلفة (شهادة راتب، بدل، استفسار).</p>
    </div>
    <div class="d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/employeeRequest/importExcel" method="POST" enctype="multipart/form-data" id="excelForm" style="display:none;">
            <input type="file" id="excelUpload" name="excel_file" accept=".xlsx, .xls, .csv" onchange="document.getElementById('excelForm').submit()">
        </form>
        <button class="btn btn-success" onclick="document.getElementById('excelUpload').click()"><i class="fas fa-file-excel"></i> استيراد</button>
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-file-pdf"></i> PDF</button>
        <a href="<?php echo URLROOT; ?>/employeeRequest/create" class="btn btn-primary"><i class="fas fa-plus"></i> تقديم طلب جديد</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">سجل طلبات الموظفين</h2>
    <h5 class="text-muted font-monospace">تاريخ: <?php echo date('Y-m-d'); ?></h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف (مقدم الطلب)</th>
                        <th>نوع الطلب</th>
                        <th>التفاصيل</th>
                        <th class="text-center">تاريخ التقديم</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center d-print-none">إجراء الـ HR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as$r) : 
                        $statusClass = match($r->status) { 'approved' => 'badge-success', 'rejected' => 'badge-danger', default => 'badge-warning' };
                        $statusLabel = match($r->status) { 'approved' => 'موافق عليه', 'rejected' => 'مرفوض', default => 'قيد الانتظار' };
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="fas fa-user-circle text-muted me-1"></i> <?php echo htmlspecialchars($r->employee_name); ?></td>
                        <td class="fw-bold text-primary"><?php echo htmlspecialchars($r->request_type); ?></td>
                        <td class="text-muted" style="font-size:13px; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo htmlspecialchars($r->details); ?>">
                            <?php echo htmlspecialchars($r->details); ?>
                        </td>
                        <td class="text-center font-monospace fs-6 text-muted"><?php echo date('Y-m-d H:i', strtotime($r->created_at)); ?></td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td class="text-center d-print-none">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/employeeRequest/edit/<?php echo $r->id; ?>" class="btn-icon edit" title="الرد أو التعديل"><i class="fas fa-reply"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/employeeRequest/delete/<?php echo $r->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد مسح الطلب؟');">
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