<?php
// المسار: app/views/tickets/index.php
$tickets = $data['tickets'] ?? [];
?>

<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-headset" style="color:var(--primary);"></i> نظام تذاكر الدعم الفني
        </h3>
        <p style="margin:4px 0 0; font-size:13px; color:var(--text-muted);">متابعة شكاوى واستفسارات العملاء ومستوى الخدمة (SLA)</p>
    </div>
    
    <div style="display:flex; gap:10px;">
        <a href="<?php echo URL_ROOT; ?>/ticket/create" style="padding:10px 20px; background:var(--primary); color:#fff; border-radius:8px; text-decoration:none; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; transition:0.2s; box-shadow:0 4px 10px rgba(20,184,166,0.2);">
            <i class="fas fa-plus"></i> تذكرة جديدة
        </a>
    </div>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">رقم التذكرة</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">العنوان / العميل</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">الأولوية</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">المسؤول (الموظف)</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">الحالة</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; text-align:center;">إجراء السريع</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($tickets)): foreach($tickets as $t): 
                    // تنسيق الأولويات
                    $priorityClass = match($t->priority) {
                        'urgent' => 'color:var(--danger); font-weight:800;',
                        'high' => 'color:var(--accent); font-weight:700;',
                        'medium' => 'color:var(--info); font-weight:600;',
                        'low' => 'color:var(--text-muted);',
                        default => ''
                    };
                    $priorityLabel = match($t->priority) {
                        'urgent' => '<i class="fas fa-fire"></i> عاجل جداً',
                        'high' => '<i class="fas fa-arrow-up"></i> مرتفعة',
                        'medium' => '<i class="fas fa-minus"></i> متوسطة',
                        'low' => '<i class="fas fa-arrow-down"></i> منخفضة',
                        default => $t->priority
                    };

                    // تنسيق الحالات
                    $statusClass = match($t->status) {
                        'open' => 'background:var(--danger-light); color:var(--danger);',
                        'in_progress' => 'background:var(--info-light); color:var(--info-dark);',
                        'resolved' => 'background:var(--success-light); color:var(--success);',
                        'closed' => 'background:var(--page-bg); color:var(--text-muted);',
                        default => ''
                    };
                    $statusLabel = match($t->status) {
                        'open' => 'مفتوحة',
                        'in_progress' => 'قيد المعالجة',
                        'resolved' => 'محلولة',
                        'closed' => 'مغلقة',
                        default => $t->status
                    };
                ?>
                <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;">
                    <td style="padding:14px 20px; font-family:monospace; font-weight:800; color:var(--text-dark); font-size:13px;">
                        <span style="background:var(--page-bg); padding:4px 8px; border-radius:6px; border:1px solid var(--border);"><?php echo htmlspecialchars($t->ticket_number); ?></span>
                    </td>
                    <td style="padding:14px 20px;">
                        <div style="font-weight:700; color:var(--text-dark); font-size:14px; margin-bottom:2px;"><?php echo htmlspecialchars($t->subject); ?></div>
                        <div style="font-size:11px; color:var(--text-muted);"><i class="fas fa-user"></i> <?php echo htmlspecialchars($t->customer_name ?? 'بدون عميل مرتبط'); ?></div>
                    </td>
                    <td style="padding:14px 20px;">
                        <span style="<?php echo $priorityClass; ?> font-size:12px;"><?php echo $priorityLabel; ?></span>
                    </td>
                    <td style="padding:14px 20px; font-size:13px; color:var(--text-body); font-weight:600;">
                        <?php if($t->assigned_to_name): ?>
                            <i class="fas fa-user-tie" style="color:var(--primary); margin-left:4px;"></i> <?php echo htmlspecialchars($t->assigned_to_name); ?>
                        <?php else: ?>
                            <span style="color:var(--danger); font-size:12px;"><i class="fas fa-exclamation-triangle"></i> غير معين</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:14px 20px;">
                        <span style="padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                    </td>
                    <td style="padding:14px 20px; text-align:center;">
                        <?php if ($t->status === 'open'): ?>
                            <form action="<?php echo URL_ROOT; ?>/ticket/changeStatus/<?php echo $t->id; ?>" method="POST" style="display:inline;">
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit" style="border:none; background:var(--info-light); color:var(--info-dark); width:32px; height:32px; border-radius:6px; cursor:pointer;" title="بدء العمل"><i class="fas fa-play"></i></button>
                            </form>
                        <?php elseif ($t->status === 'in_progress'): ?>
                            <form action="<?php echo URL_ROOT; ?>/ticket/changeStatus/<?php echo $t->id; ?>" method="POST" style="display:inline;">
                                <input type="hidden" name="status" value="resolved">
                                <button type="submit" style="border:none; background:var(--success-light); color:var(--success); width:32px; height:32px; border-radius:6px; cursor:pointer;" title="تحديد كمحلولة"><i class="fas fa-check-double"></i></button>
                            </form>
                        <?php else: ?>
                             <span style="color:var(--text-muted); font-size:12px;"><i class="fas fa-lock"></i> مقفلة</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:60px 20px;">
                        <i class="fas fa-headset" style="font-size:40px; color:var(--border); margin-bottom:12px;"></i>
                        <h4 style="margin:0 0 6px; font-size:15px; color:var(--text-dark);">لا توجد تذاكر دعم فني</h4>
                        <p style="margin:0; font-size:13px; color:var(--text-muted);">جميع الشكاوى والطلبات تم حلها أو لا يوجد أي طلبات حالية.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>