<?php
// app/views/followups/index.php
$followups = $data['followups'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-calendar-check text-primary"></i> جدول متابعات العملاء</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة المواعيد، المكالمات، والاجتماعات المجدولة مع العملاء المحتملين.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/followup/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> جدولة متابعة
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
                        <th class="text-center" style="width: 50px;">النوع</th>
                        <th>العميل المحتمل</th>
                        <th>الموعد المجدول</th>
                        <th>ملاحظات</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($followups as $f): 
                        $icon = match($f->type) { 'call' => 'fa-phone text-success', 'meeting' => 'fa-handshake text-primary', 'email' => 'fa-envelope text-warning', default => 'fa-bell text-muted' };
                        $isPast = strtotime($f->scheduled_at) < time() && $f->status === 'pending';
                    ?>
                    <tr style="<?php echo $isPast ? 'background-color: #fef2f2;' : ''; ?>">
                        <td class="text-center fs-4"><i class="fas <?php echo $icon; ?>"></i></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($f->lead_name ?? 'محذوف'); ?></div>
                            <div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars($f->lead_company ?? ''); ?></div>
                        </td>
                        <td class="font-monospace fw-bold <?php echo $isPast ? 'text-danger' : 'text-dark'; ?>" style="direction:ltr; text-align:right;">
                            <?php echo date('Y-m-d h:i A', strtotime($f->scheduled_at)); ?>
                            <?php if($isPast): ?><i class="fas fa-exclamation-circle text-danger ms-1" title="موعد متأخر"></i><?php endif; ?>
                        </td>
                        <td class="text-muted" style="font-size: 13px; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo htmlspecialchars($f->notes ?? '—'); ?>
                        </td>
                        <td class="text-center">
                            <?php if($f->status === 'completed'): ?>
                                <span class="badge badge-success"><i class="fas fa-check-double"></i> تمت المتابعة</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><i class="fas fa-clock"></i> قيد الانتظار</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <?php if($f->status === 'pending'): ?>
                                <form action="<?php echo URLROOT; ?>/followup/complete/<?php echo $f->id; ?>" method="POST" style="display:inline;">
                                    <button type="submit" class="btn-icon view text-success" style="border-color: var(--success);" title="تحديد كـ تمت المتابعة"><i class="fas fa-check"></i></button>
                                </form>
                                <?php endif; ?>
                                
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/followup/delete/<?php echo $f->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد الحذف؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if(empty($followups)): ?>
                    <tr><td colspan="6" class="text-center text-muted p-5"><i class="fas fa-calendar-times fs-1 mb-3 opacity-50 d-block"></i> لا يوجد أي متابعات مجدولة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>