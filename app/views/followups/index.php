<?php
// app/views/followups/index.php
$followups = $data['followups'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-phone-volume text-primary"></i> جدول المتابعات والاجتماعات</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تذكيرات وإدارة الأنشطة الخاصة بالعملاء المحتملين.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/followup/create" class="btn btn-primary">
        <i class="fas fa-calendar-plus"></i> جدولة متابعة
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>التاريخ والوقت</th>
                        <th>العميل المحتمل</th>
                        <th class="text-center">النوع</th>
                        <th>الملاحظات</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($followups)): foreach($followups as $f): 
                        $typeIcon = match($f->type) {
                            'call' => '<i class="fas fa-phone text-info"></i> مكالمة',
                            'meeting' => '<i class="fas fa-handshake text-primary"></i> اجتماع',
                            'email' => '<i class="fas fa-envelope text-accent"></i> إيميل',
                            default => $f->type
                        };
                        $isPast = strtotime($f->scheduled_date) < time() && $f->status == 'pending';
                        $rowStyle = $f->status == 'completed' ? 'opacity: 0.6; background-color: #f8fafc;' : '';
                    ?>
                    <tr style="<?php echo $rowStyle; ?>">
                        <td class="font-monospace fw-bold <?php echo $isPast ? 'text-danger' : 'text-dark'; ?>">
                            <?php echo date('Y-m-d H:i', strtotime($f->scheduled_date)); ?>
                            <?php if($isPast) echo '<br><span class="text-danger" style="font-size:10px; font-family:Cairo;">(متأخرة)</span>'; ?>
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($f->lead_name); ?></div>
                            <div style="font-size:12px; color:var(--text-muted);"><?php echo htmlspecialchars($f->company ?? ''); ?></div>
                        </td>
                        <td class="text-center fw-bold"><?php echo $typeIcon; ?></td>
                        <td class="text-muted fs-6"><?php echo htmlspecialchars($f->notes); ?></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <?php if($f->status == 'pending'): ?>
                                    <form action="<?php echo URLROOT; ?>/followup/complete/<?php echo $f->id; ?>" method="POST" style="display:inline;">
                                        <button type="submit" class="btn btn-success" style="padding: 4px 10px; font-size: 11px;"><i class="fas fa-check"></i> إنجاز</button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><i class="fas fa-check-double"></i> مكتملة</span>
                                <?php endif; ?>
                                
                                <form action="<?php echo URLROOT; ?>/followup/delete/<?php echo $f->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('حذف هذه المتابعة؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5" class="text-center text-muted" style="padding: 40px;">لا توجد متابعات مجدولة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>