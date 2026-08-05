<?php
// المسار: app/views/followups/index.php
/** @var array $data */
$followups = $data['followups'] ?? [];
?>

<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-phone-volume" style="color:var(--primary);"></i> جدول المتابعات (Follow-ups)
        </h3>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="<?php echo URL_ROOT; ?>/followup/create" style="padding:10px 20px; background:var(--primary); color:#fff; border-radius:8px; text-decoration:none; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 10px rgba(20,184,166,0.2);">
            <i class="fas fa-calendar-plus"></i> جدولة متابعة
        </a>
    </div>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:14px 20px; font-size:11px; color:var(--text-muted);">التاريخ والوقت</th>
                    <th style="padding:14px 20px; font-size:11px; color:var(--text-muted);">العميل المحتمل</th>
                    <th style="padding:14px 20px; font-size:11px; color:var(--text-muted);">نوع المتابعة</th>
                    <th style="padding:14px 20px; font-size:11px; color:var(--text-muted);">الملاحظات</th>
                    <th style="padding:14px 20px; font-size:11px; color:var(--text-muted); text-align:center;">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($followups)): foreach($followups as $f): 
                    $typeIcon = match($f->type) {
                        'call' => '<i class="fas fa-phone" style="color:var(--info);"></i> مكالمة',
                        'meeting' => '<i class="fas fa-handshake" style="color:var(--primary);"></i> اجتماع',
                        'email' => '<i class="fas fa-envelope" style="color:var(--accent);"></i> بريد إلكتروني',
                        default => ''
                    };
                    $isPast = strtotime($f->scheduled_date) < time() && $f->status == 'pending';
                ?>
                <tr style="border-bottom:1px solid var(--border); <?php echo $f->status == 'completed' ? 'opacity:0.6; background:#f8fafc;' : ''; ?>">
                    <td style="padding:14px 20px; font-family:monospace; font-size:13px; font-weight:700; <?php echo $isPast ? 'color:var(--danger);' : 'color:var(--text-dark);'; ?>">
                        <?php echo date('Y-m-d H:i', strtotime($f->scheduled_date)); ?>
                        <?php if($isPast) echo '<br><span style="font-size:10px; color:var(--danger); font-family:Cairo;">(متأخرة)</span>'; ?>
                    </td>
                    <td style="padding:14px 20px; font-weight:600; color:var(--text-body);">
                        <?php echo htmlspecialchars($f->lead_name); ?>
                        <div style="font-size:11px; color:var(--text-muted);"><?php echo htmlspecialchars($f->company ?? ''); ?></div>
                    </td>
                    <td style="padding:14px 20px; font-size:13px; font-weight:600;"><?php echo $typeIcon; ?></td>
                    <td style="padding:14px 20px; font-size:12px; color:var(--text-muted);"><?php echo htmlspecialchars($f->notes); ?></td>
                    <td style="padding:14px 20px; text-align:center;">
                        <?php if($f->status == 'pending'): ?>
                            <form action="<?php echo URL_ROOT; ?>/followup/complete/<?php echo $f->id; ?>" method="POST" style="display:inline;">
                                <button type="submit" style="padding:6px 12px; background:var(--success-light); border:1px solid var(--success); color:var(--success); border-radius:6px; font-family:'Cairo'; font-weight:700; font-size:11px; cursor:pointer;"><i class="fas fa-check"></i> إنجاز</button>
                            </form>
                        <?php else: ?>
                            <span style="font-size:11px; font-weight:700; color:var(--text-muted);"><i class="fas fa-check-double"></i> مكتملة</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:40px; color:var(--text-muted);">لا توجد متابعات مجدولة.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>