<?php
// app/views/expenseClaim/index.php
$claims = $data['claims'] ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-receipt text-warning"></i> مطالبات المصروفات (Expense Claims)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع مطالبات الموظفين (سفر، نثريات) واعتمادها من الإدارة والمالية.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/expenseClaim/create" class="btn btn-warning"><i class="fas fa-plus"></i> تقديم مطالبة جديدة</a>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th>رقم المطالبة</th>
                    <th>الموظف</th>
                    <th>النوع / المشروع</th>
                    <th class="text-center">التاريخ</th>
                    <th class="text-left">المبلغ المسترد</th>
                    <th class="text-center">اعتماد الإدارة</th>
                    <th class="text-center">المالية (الصرف)</th>
                    <th class="text-center">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($claims as $c): 
                    $mClass = match($c->manager_approval) { 'Pending'=>'badge-warning', 'Approved'=>'badge-success', 'Rejected'=>'badge-danger', default=>'badge-secondary' };
                    $fClass = match($c->finance_approval) { 'Pending'=>'badge-warning', 'Approved'=>'badge-success', 'Rejected'=>'badge-danger', default=>'badge-secondary' };
                ?>
                <tr>
                    <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($c->claim_number); ?></td>
                    <td class="fw-bold"><i class="fas fa-user text-muted me-1"></i> <?php echo htmlspecialchars($c->employee_name ?? '—'); ?></td>
                    <td>
                        <div><?php echo htmlspecialchars($c->expense_type); ?></div>
                        <?php if($c->project_name): ?><div class="text-muted font-monospace" style="font-size:11px;">Proj: <?php echo htmlspecialchars($c->project_name); ?></div><?php endif; ?>
                    </td>
                    <td class="text-center font-monospace text-muted fs-6"><?php echo $c->claim_date; ?></td>
                    <td class="text-left font-monospace fw-bold text-danger fs-5" style="direction:ltr;"><?php echo number_format($c->amount + $c->vat_amount, 2); ?></td>
                    <td class="text-center"><span class="badge <?php echo $mClass; ?>"><?php echo $c->manager_approval; ?></span></td>
                    <td class="text-center"><span class="badge <?php echo $fClass; ?>"><?php echo $c->payment_status; ?></span></td>
                    <td class="text-center"><a href="<?php echo URLROOT; ?>/expenseClaim/show/<?php echo $c->id; ?>" class="btn-icon view"><i class="fas fa-eye"></i></a></td>
                </tr>
                <?php endforeach; if(empty($claims)): ?>
                    <tr><td colspan="8" class="text-center text-muted p-5">لا توجد مطالبات مسجلة.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>