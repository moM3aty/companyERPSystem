<?php
// app/views/advance/index.php
$advances = $data['advances'] ?? [];
$canApprove = in_array(Session::getUserRole(), ['admin', 'manager', 'super_admin']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-hand-holding-dollar text-primary"></i> السلف والعهد (Advances)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة طلبات السلف النقدية للموظفين واعتمادها لخصمها من الرواتب.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/advance/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> تقديم طلب سلفة
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
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>الموظف صاحب الطلب</th>
                        <th>التاريخ</th>
                        <th class="text-center">شهر الخصم</th>
                        <th class="text-left">المبلغ (ر.س)</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($advances as $adv) : 
                        $statusClass = match($adv->status) {
                            'approved' => 'badge-success',
                            'rejected' => 'badge-danger',
                            'deducted' => 'badge-dark',
                            default => 'badge-warning'
                        };
                        $statusLabel = match($adv->status) {
                            'approved' => 'مقبولة',
                            'rejected' => 'مرفوضة',
                            'deducted' => 'تم الخصم (منتهية)',
                            default => 'قيد الانتظار'
                        };
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="fas fa-user-circle text-muted me-1"></i> <?php echo htmlspecialchars($adv->employee_name); ?></td>
                        <td class="text-muted font-monospace fs-6"><?php echo date('Y-m-d', strtotime($adv->date)); ?></td>
                        <td class="text-center font-monospace fw-bold text-info">
                            <?php echo $adv->deduction_month . ' / ' . $adv->deduction_year; ?>
                        </td>
                        <td class="font-monospace fw-bold text-primary text-left fs-5" style="direction:ltr;">
                            <?php echo number_format($adv->amount, 2); ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                            <?php if(!empty($adv->approver_name)): ?>
                                <div style="font-size: 10px; color: #94a3b8; margin-top: 4px;">بواسطة: <?php echo htmlspecialchars($adv->approver_name); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <?php if($canApprove && $adv->status === 'pending'): ?>
                                    <form action="<?php echo URLROOT; ?>/advance/updateStatus" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد الموافقة على السلفة وسيتم إدراجها للخصم في الراتب؟');">
                                        <input type="hidden" name="advance_id" value="<?php echo $adv->id; ?>">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn-icon view text-success" style="border-color:var(--success);" title="موافقة"><i class="fas fa-check"></i></button>
                                    </form>
                                    <form action="<?php echo URLROOT; ?>/advance/updateStatus" method="POST" style="display:inline;">
                                        <input type="hidden" name="advance_id" value="<?php echo $adv->id; ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn-icon delete text-danger" title="رفض"><i class="fas fa-times"></i></button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/advance/delete/<?php echo $adv->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد إلغاء وحذف السلفة؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف السجل"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($advances)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-coins fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد طلبات سلف مسجلة حالياً.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>