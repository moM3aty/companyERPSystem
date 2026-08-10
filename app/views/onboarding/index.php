<?php
// app/views/onboarding/index.php
$onboarding = $data['onboarding'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-clipboard-check text-success"></i> تهيئة الموظفين (Onboarding Checklist)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تأكد من استكمال كافة الإجراءات وتسليم العهد للموظف الجديد.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/onboarding/importExcel" method="POST" enctype="multipart/form-data" id="excelForm" style="display:none;">
            <input type="file" id="excelUpload" name="excel_file" accept=".xlsx, .xls, .csv" onchange="document.getElementById('excelForm').submit()">
        </form>
        <button class="btn btn-success" onclick="document.getElementById('excelUpload').click()"><i class="fas fa-file-excel"></i> استيراد</button>
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-file-pdf"></i> PDF</button>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">تقرير مهام تهيئة الموظفين الجدد</h2>
    <h5 class="text-muted font-monospace">تاريخ التقرير: <?php echo date('Y-m-d'); ?></h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف</th>
                        <th>المنصب</th>
                        <th class="text-center">التقدم (Progress)</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center d-print-none">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($onboarding as $o) : 
                        $total = 10;
                        $completed = $o->contract_signed + $o->id_received + $o->bank_details + $o->email_created + $o->equipment_issued + $o->access_card + $o->system_accounts + $o->orientation + $o->safety_training + $o->manager_assigned;
                        $percent = ($completed / $total) * 100;
                        $statusClass = $o->status === 'completed' ? 'badge-success' : 'badge-warning';
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="fas fa-user-circle text-muted me-1"></i> <?php echo htmlspecialchars($o->employee_name); ?></td>
                        <td class="text-muted"><?php echo htmlspecialchars($o->position ?? '—'); ?></td>
                        <td class="text-center">
                            <div class="progress" style="height: 10px; background: var(--slate-200); border-radius: 5px; width: 120px; margin: 0 auto 5px;">
                                <div class="progress-bar <?php echo $percent == 100 ? 'bg-success' : 'bg-primary'; ?>" style="width: <?php echo $percent; ?>%;"></div>
                            </div>
                            <span class="text-muted font-monospace" style="font-size:11px;"><?php echo $completed; ?>/10 منجز</span>
                        </td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $o->status === 'completed' ? 'مكتملة' : 'قيد المتابعة'; ?></span></td>
                        <td class="text-center d-print-none">
                            <a href="<?php echo URLROOT; ?>/onboarding/edit/<?php echo $o->id; ?>" class="btn-icon edit" title="تحديث القائمة"><i class="fas fa-tasks"></i></a>
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