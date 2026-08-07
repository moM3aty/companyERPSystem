<?php
// app/views/sanctions/index.php
$sanctions = $sanctions ?? ($data['sanctions'] ?? []);
$totalDeductions = $totalDeductions ?? ($data['total_deductions'] ?? 0);
$warningsCount = $warningsCount ?? ($data['warnings_count'] ?? 0);
$isAdmin = $isAdmin ?? ($data['is_admin'] ?? false);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-gavel text-danger"></i> سجل الجزاءات والمخالفات</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">متابعة الإنذارات والخصومات المالية للموظفين لضبط الانضباط الإداري.</p>
    </div>
    <?php if($isAdmin): ?>
    <a href="<?php echo URLROOT; ?>/sanction/create" class="btn btn-danger">
        <i class="fas fa-plus"></i> توقيع جزاء جديد
    </a>
    <?php endif; ?>
</div>

<div class="form-grid mb-4">
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--danger-light); color: var(--danger); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 22px; font-weight: 800; color: var(--danger);" class="font-monospace text-right" style="direction:ltr;"><?php echo number_format($totalDeductions, 2); ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">إجمالي الخصومات المالية</span>
            </div>
        </div>
    </div>
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--accent-light); color: var(--accent); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 22px; font-weight: 800;"><?php echo $warningsCount; ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">عدد الإنذارات الموجهة</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>الموظف المخالف</th>
                        <th class="text-center">نوع الجزاء</th>
                        <th>المبلغ المخصوم</th>
                        <th>سبب المخالفة</th>
                        <th class="text-center">بواسطة</th>
                        <?php if($isAdmin): ?><th class="text-center">إجراءات</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($sanctions)): foreach($sanctions as $s): 
                        $isDeduction = $s->type === 'deduction';
                        $typeClass = $isDeduction ? 'badge-danger' : 'badge-warning';
                        $typeLabel = $isDeduction ? '<i class="fas fa-money-bill-transfer"></i> خصم مالي' : '<i class="fas fa-exclamation-circle"></i> إنذار شفوي/كتابي';
                    ?>
                    <tr>
                        <td class="text-muted fs-6"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($s->date)); ?></td>
                        <td class="fw-bold text-dark"><i class="fas fa-user-tag text-muted me-1"></i> <?php echo htmlspecialchars($s->employee_name); ?></td>
                        <td class="text-center"><span class="badge <?php echo $typeClass; ?>"><?php echo $typeLabel; ?></span></td>
                        <td>
                            <?php if($isDeduction): ?>
                                <span class="font-monospace fw-bold text-danger" style="direction:ltr; text-align:right; display:inline-block;">-<?php echo number_format($s->amount, 2); ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted" style="font-size: 13px; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($s->reason); ?>">
                            <?php echo htmlspecialchars($s->reason); ?>
                        </td>
                        <td class="text-center text-muted" style="font-size: 12px;"><i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($s->created_by_name ?? 'النظام'); ?></td>
                        
                        <?php if($isAdmin): ?>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/sanction/edit/<?php echo $s->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <form action="<?php echo URLROOT; ?>/sanction/delete/<?php echo $s->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد إلغاء وحذف الجزاء؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="<?php echo $isAdmin ? '7' : '6'; ?>" class="text-center text-muted" style="padding: 40px;">لا توجد أي جزاءات أو مخالفات مسجلة.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>