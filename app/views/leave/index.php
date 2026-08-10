<?php
// app/views/leave/index.php
$leaves = $data['leaves'] ?? [];
$userRole = Session::getUserRole();
$canApprove = in_array($userRole, ['admin', 'manager', 'super_admin']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-calendar-minus text-primary"></i> طلبات الإجازات</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">متابعة وإدارة طلبات الإجازات للموظفين.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/leave/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> تقديم طلب إجازة
    </a>
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

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف</th>
                        <th>نوع الإجازة</th>
                        <th>الفترة (من - إلى)</th>
                        <th class="text-center">المدة (أيام)</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaves as $l) : 
                        $statusClass = match($l->status) {
                            'approved' => 'badge-success',
                            'rejected' => 'badge-danger',
                            default => 'badge-warning'
                        };
                        $statusLabel = match($l->status) {
                            'approved' => 'مقبول',
                            'rejected' => 'مرفوض',
                            default => 'قيد الانتظار'
                        };
                        
                        $typeLabel = match($l->leave_type) {
                            'annual' => 'سنوية', 'sick' => 'مرضية', 'unpaid' => 'بدون راتب', 'maternity' => 'أمومة', default => $l->leave_type
                        };
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="fas fa-user-circle text-muted"></i> <?php echo htmlspecialchars($l->employee_name ?? 'أنا'); ?></td>
                        <td><span class="badge badge-secondary"><?php echo $typeLabel; ?></span></td>
                        <td class="text-muted font-monospace fs-6">
                            <?php echo $l->start_date; ?> <i class="fas fa-arrow-left mx-1" style="font-size:10px;"></i> <?php echo $l->end_date; ?>
                        </td>
                        <td class="text-center font-monospace fw-bold text-primary"><?php echo $l->days; ?></td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <?php if($canApprove && $l->status === 'pending'): ?>
                                    <form action="<?php echo URLROOT; ?>/leave/updateStatus/<?php echo $l->id; ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn-icon view text-success" title="موافقة"><i class="fas fa-check"></i></button>
                                    </form>
                                    <form action="<?php echo URLROOT; ?>/leave/updateStatus/<?php echo $l->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد رفض الإجازة؟');">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn-icon delete text-danger" title="رفض"><i class="fas fa-times"></i></button>
                                    </form>
                                <?php endif; ?>

                                <?php if($l->status === 'pending' || Session::hasRole('admin')): ?>
                                    <form action="<?php echo URLROOT; ?>/leave/delete/<?php echo $l->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد إلغاء هذا الطلب؟');">
                                        <button type="submit" class="btn-icon delete" title="إلغاء الطلب"><i class="fas fa-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($leaves)) : ?>
                    <tr><td colspan="6" class="text-center text-muted p-5"><i class="fas fa-calendar-xmark fs-1 mb-3 opacity-50 d-block"></i> لا توجد طلبات إجازة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>