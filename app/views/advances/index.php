<?php
// app/views/advances/index.php
$advances = $advances ?? ($data['advances'] ?? []);
$isAdmin = $isAdmin ?? ($data['is_admin'] ?? false);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-hand-holding-dollar text-accent"></i> سجل السلف والعهد</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">متابعة طلبات السلف للموظفين وخصمها من الراتب.</p>
    </div>
    <div>
        <a href="<?php echo URLROOT; ?>/advance/create" class="btn btn-warning">
            <i class="fas fa-plus"></i> طلب سلفة
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>رقم السلفة</th>
                        <th>الموظف</th>
                        <th class="text-left">المبلغ المطلوب</th>
                        <th>تاريخ الطلب</th>
                        <th class="text-center">شهر الخصم</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($advances)): foreach($advances as $adv): 
                        $statusClass = match($adv->status) {
                            'approved' => 'badge-success',
                            'rejected' => 'badge-danger',
                            'deducted' => 'badge-purple',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($adv->status) {
                            'approved' => '<i class="fas fa-check"></i> معتمد',
                            'rejected' => '<i class="fas fa-times"></i> مرفوض',
                            'deducted' => '<i class="fas fa-check-double"></i> تم الخصم',
                            default => '<i class="fas fa-clock"></i> قيد المراجعة'
                        };
                    ?>
                    <tr>
                        <td class="font-monospace text-muted fw-bold">ADV-<?php echo str_pad($adv->id, 4, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($adv->employee_name); ?></div>
                            <div style="font-size:11px; color:var(--text-muted);"><?php echo htmlspecialchars($adv->reason ?? 'بدون سبب'); ?></div>
                        </td>
                        <td class="font-monospace fw-bold text-danger fs-5" style="direction:ltr; text-align:right;">
                            <?php echo number_format($adv->amount, 2); ?>
                        </td>
                        <td class="text-muted fs-6"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($adv->date)); ?></td>
                        <td class="text-center font-monospace fw-bold">
                            <?php echo $adv->deduction_month . ' / ' . $adv->deduction_year; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <?php if ($adv->status === 'pending'): ?>
                                    <?php if ($isAdmin): ?>
                                    <form method="POST" action="<?php echo URLROOT; ?>/advance/approve/<?php echo $adv->id; ?>" style="display:inline;" onsubmit="return confirm('تأكيد الموافقة؟');">
                                        <button type="submit" class="btn-icon view" title="اعتماد"><i class="fas fa-check text-success"></i></button>
                                    </form>
                                    <form method="POST" action="<?php echo URLROOT; ?>/advance/reject/<?php echo $adv->id; ?>" style="display:inline;" onsubmit="return confirm('تأكيد الرفض؟');">
                                        <button type="submit" class="btn-icon delete" title="رفض"><i class="fas fa-times text-danger"></i></button>
                                    </form>
                                    <?php endif; ?>
                                    <a href="<?php echo URLROOT; ?>/advance/edit/<?php echo $adv->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                    <?php if ($isAdmin): ?>
                                    <form action="<?php echo URLROOT; ?>/advance/delete/<?php echo $adv->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد الحذف؟');">
                                        <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                    </form>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size:12px;"><i class="fas fa-lock"></i> مقفل</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted" style="padding: 40px;">لا توجد سلف مسجلة.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>