<?php
// app/views/hrDocument/index.php
$documents =$data['documents'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-id-card text-primary"></i> الوثائق الرسمية (Official Documents)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة جوازات السفر، الإقامات، والتأشيرات وتتبع تواريخ انتهائها.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/hrDocument/importExcel" method="POST" enctype="multipart/form-data" id="excelForm" style="display:none;">
            <input type="file" id="excelUpload" name="excel_file" accept=".xlsx, .xls, .csv" onchange="document.getElementById('excelForm').submit()">
        </form>
        <button class="btn btn-success" onclick="document.getElementById('excelUpload').click()"><i class="fas fa-file-excel"></i> استيراد</button>
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-file-pdf"></i> PDF</button>
        <a href="<?php echo URLROOT; ?>/hrDocument/create" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة وثيقة</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">تقرير الوثائق الرسمية للموظفين</h2>
    <h5 class="text-muted font-monospace">تاريخ التقرير: <?php echo date('Y-m-d'); ?></h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف</th>
                        <th>نوع الوثيقة (Type)</th>
                        <th>رقم الوثيقة (Number)</th>
                        <th class="text-center">تاريخ الانتهاء</th>
                        <th class="text-center">حالة الوثيقة</th>
                        <th class="text-center d-print-none">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as$d) : 
                        $days =$d->days_to_expire;
                        $alertClass = 'badge-success';$alertText = '🟢 سارية (Valid)';
                        if ($days !== null) {
                            if ($days < 0) { $alertClass = 'badge-danger';$alertText = '🔴 منتهية (Expired)'; }
                            elseif ($days <= 30) {$alertClass = 'badge-danger'; $alertText = "🔴 تنتهي خلال $days يوم"; }
                            elseif ($days <= 60) {$alertClass = 'badge-warning'; $alertText = "🟠 تنتهي خلال $days يوم"; }
                        }
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="fas fa-user-tie text-muted me-1"></i> <?php echo htmlspecialchars($d->employee_name); ?></td>
                        <td class="fw-bold text-primary"><?php echo htmlspecialchars($d->doc_type); ?></td>
                        <td class="font-monospace text-muted"><?php echo htmlspecialchars($d->doc_number); ?></td>
                        <td class="text-center font-monospace fs-6"><?php echo $d->expiry_date ?? '—'; ?></td>
                        <td class="text-center"><span class="badge <?php echo $alertClass; ?>"><?php echo $alertText; ?></span></td>
                        <td class="text-center d-print-none">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/hrDocument/edit/<?php echo $d->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/hrDocument/delete/<?php echo $d->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد مسح الوثيقة؟');">
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