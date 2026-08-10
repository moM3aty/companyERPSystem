<?php
// app/views/assetAssignment/index.php
$assets = $data['assets'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-laptop text-warning"></i> العهد والأصول المسلمة (Assets Assigned)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة اللابتوبات، الهواتف، السيارات، وبطاقات الدخول المسلمة للموظفين.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/assetAssignment/importExcel" method="POST" enctype="multipart/form-data" id="excelForm" style="display:none;">
            <input type="file" id="excelUpload" name="excel_file" accept=".xlsx, .xls, .csv" onchange="document.getElementById('excelForm').submit()">
        </form>
        <button class="btn btn-success" onclick="document.getElementById('excelUpload').click()"><i class="fas fa-file-excel"></i> استيراد</button>
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-file-pdf"></i> PDF</button>
        <a href="<?php echo URLROOT; ?>/assetAssignment/create" class="btn btn-primary"><i class="fas fa-plus"></i> تسليم عهدة</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">تقرير العهد والأصول المسلمة للموظفين</h2>
    <h5 class="text-muted font-monospace">تاريخ التقرير: <?php echo date('Y-m-d'); ?></h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف</th>
                        <th>نوع العهدة (Asset Type)</th>
                        <th>الكود / السيريال (Asset ID)</th>
                        <th class="text-center">تاريخ التسليم</th>
                        <th class="text-center">تاريخ الإرجاع</th>
                        <th class="text-center">الحالة (Status)</th>
                        <th class="text-center d-print-none">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assets as $a) : 
                        $statusClass = match($a->status) { 'Assigned' => 'badge-primary', 'Returned' => 'badge-success', 'Damaged' => 'badge-warning', 'Lost' => 'badge-danger', default => 'badge-secondary' };
                        $statusLabel = match($a->status) { 'Assigned' => 'مُسلمة (Assigned)', 'Returned' => 'مُسترجعة (Returned)', 'Damaged' => 'تالفة (Damaged)', 'Lost' => 'مفقودة (Lost)', default => $a->status };
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="fas fa-user-circle text-muted me-1"></i> <?php echo htmlspecialchars($a->employee_name); ?></td>
                        <td class="fw-bold text-info"><?php echo htmlspecialchars($a->asset_type); ?></td>
                        <td class="font-monospace text-muted"><?php echo htmlspecialchars($a->asset_id); ?></td>
                        <td class="text-center font-monospace fs-6"><?php echo $a->issue_date; ?></td>
                        <td class="text-center font-monospace fs-6 text-danger fw-bold"><?php echo $a->return_date ?? '—'; ?></td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td class="text-center d-print-none">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/assetAssignment/edit/<?php echo $a->id; ?>" class="btn-icon edit" title="تحديث / إرجاع"><i class="fas fa-exchange-alt"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/assetAssignment/delete/<?php echo $a->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد مسح السجل؟');">
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