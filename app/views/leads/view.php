<?php
// المسار: app/views/leads/view.php
$lead = $data['lead'] ?? null;
$followUps = $data['follow_ups'] ?? [];

$statusClass = match ($lead->status) {
    'new' => 'badge-info',
    'contacted' => 'badge-primary',
    'qualified' => 'badge-success',
    'lost' => 'badge-danger',
    'converted' => 'badge-purple',
    default => 'badge-secondary'
};
$statusLabel = match ($lead->status) {
    'new' => 'جديد',
    'contacted' => 'تم التواصل',
    'qualified' => 'مؤهل',
    'lost' => 'مفقود',
    'converted' => 'تم التحويل',
    default => $lead->status
};
?>

<div style="display:grid; grid-template-columns: 1fr 2fr; gap:24px; max-width:1100px; margin:0 auto;">

    <!-- الجانب الأيمن: تفاصيل العميل وحالته -->
    <div style="display:flex; flex-direction:column; gap:24px;">
        <div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
            <div style="padding:24px; border-bottom:1px solid var(--border); background:linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color:#fff;">
                <h2 style="margin:0 0 5px; font-size:20px; font-weight:800; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($lead->name); ?>
                </h2>
                <div style="font-size:13px; opacity:0.9;"><i class="fas fa-building"></i> <?php echo htmlspecialchars($lead->company ?? 'لا توجد شركة'); ?></div>
            </div>

            <div style="padding:20px; display:flex; flex-direction:column; gap:16px;">
                <div>
                    <span style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase;">حالة العميل (Status)</span>
                    <div style="margin-top:4px;">
                        <span class="badge <?php echo $statusClass; ?>" style="padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: bold;"><?php echo $statusLabel; ?></span>
                    </div>
                </div>

                <div>
                    <span style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase;">تحديث الحالة</span>
                    <form action="<?php echo URLROOT; ?>/lead/changeStatus/<?php echo $lead->id; ?>" method="POST" style="display:flex; gap:8px; margin-top:4px;">
                        <select name="status" style="flex:1; padding:8px 12px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none; font-size:13px;">
                            <option value="new" <?php echo $lead->status == 'new' ? 'selected' : ''; ?>>جديد</option>
                            <option value="contacted" <?php echo $lead->status == 'contacted' ? 'selected' : ''; ?>>تم التواصل</option>
                            <option value="qualified" <?php echo $lead->status == 'qualified' ? 'selected' : ''; ?>>مؤهل</option>
                            <option value="lost" <?php echo $lead->status == 'lost' ? 'selected' : ''; ?>>مفقود</option>
                            <option value="converted" <?php echo $lead->status == 'converted' ? 'selected' : ''; ?>>تم التحويل لعميل فعلي</option>
                        </select>
                        <button type="submit" style="padding:8px 12px; background:var(--page-bg); border:1px solid var(--border); border-radius:6px; cursor:pointer; font-family:'Cairo'; font-weight:600;"><i class="fas fa-check"></i></button>
                    </form>
                </div>

                <hr style="border:0; border-top:1px dashed var(--border); margin:0;">

                <div style="display:flex; align-items:center; gap:10px; font-size:14px;">
                    <div style="width:30px; height:30px; border-radius:6px; background:var(--info-light); color:var(--info-dark); display:flex; align-items:center; justify-content:center;"><i class="fas fa-phone"></i></div>
                    <span style="direction:ltr; font-weight:600; color:var(--text-dark);"><?php echo htmlspecialchars($lead->phone ?? '—'); ?></span>
                </div>

                <div style="display:flex; align-items:center; gap:10px; font-size:14px;">
                    <div style="width:30px; height:30px; border-radius:6px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center;"><i class="fas fa-envelope"></i></div>
                    <span style="direction:ltr; font-weight:600; color:var(--text-dark);"><?php echo htmlspecialchars($lead->email ?? '—'); ?></span>
                </div>

                <div style="display:flex; align-items:center; gap:10px; font-size:14px;">
                    <div style="width:30px; height:30px; border-radius:6px; background:var(--purple-light); color:var(--purple); display:flex; align-items:center; justify-content:center;"><i class="fas fa-user-tie"></i></div>
                    <span style="font-weight:600; color:var(--text-dark);"><?php echo htmlspecialchars($lead->assigned_name ?? 'غير معين'); ?></span>
                </div>

                <?php if ($lead->notes): ?>
                    <div style="background:#f8fafc; padding:12px; border-radius:8px; border:1px solid var(--border); font-size:13px; color:var(--text-body); line-height:1.6;">
                        <strong>ملاحظات التسجيل:</strong><br>
                        <?php echo nl2br(htmlspecialchars($lead->notes)); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- الجانب الأيسر: إضافة متابعة والجدول الزمني -->
    <div style="display:flex; flex-direction:column; gap:24px;">

        <!-- فورم تسجيل متابعة جديدة -->
        <div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); background:#f8fafc;">
                <h3 style="margin:0; font-size:15px; font-weight:700; color:var(--text-dark);"><i class="fas fa-phone-volume" style="color:var(--success);"></i> تسجيل متابعة أو اجتماع</h3>
            </div>
            <form action="<?php echo URLROOT; ?>/lead/addFollowUp/<?php echo $lead->id; ?>" method="POST" style="padding:20px;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

                    <div style="display:flex; flex-direction:column; gap:6px;">
                        <label style="font-size:12px; font-weight:700; color:var(--text-body);">تاريخ المتابعة</label>
                        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required style="padding:8px 12px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;">
                    </div>

                    <div style="display:flex; flex-direction:column; gap:6px;">
                        <label style="font-size:12px; font-weight:700; color:var(--text-body);">طريقة التواصل</label>
                        <select name="type" style="padding:8px 12px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;">
                            <option value="call">مكالمة هاتفية</option>
                            <option value="meeting">اجتماع / زيارة</option>
                            <option value="email">بريد إلكتروني / رسالة</option>
                        </select>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:6px; grid-column:1/-1;">
                        <label style="font-size:12px; font-weight:700; color:var(--text-body);">تفاصيل المتابعة وملخص الحديث <span style="color:var(--danger);">*</span></label>
                        <textarea name="notes" required rows="3" style="padding:8px 12px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;" placeholder="اكتب ما تم الاتفاق عليه أو ملخص الحديث..."></textarea>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:6px; grid-column:1/-1;">
                        <label style="font-size:12px; font-weight:700; color:var(--text-body);">تاريخ الإجراء القادم (إن وجد)</label>
                        <input type="date" name="next_action_date" style="padding:8px 12px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;">
                    </div>

                    <div style="grid-column:1/-1; text-align:left;">
                        <button type="submit" style="padding:10px 20px; background:var(--text-dark); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:700; cursor:pointer;"><i class="fas fa-paper-plane"></i> حفظ المتابعة</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- سجل المتابعات (Timeline) -->
        <div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); background:#f8fafc;">
                <h3 style="margin:0; font-size:15px; font-weight:700; color:var(--text-dark);"><i class="fas fa-history" style="color:var(--info);"></i> سجل المتابعات (Timeline)</h3>
            </div>
            <div style="padding:24px;">
                <?php if (!empty($followUps)): ?>
                    <div style="position:relative; border-right:2px solid var(--border); padding-right:20px; margin-right:10px;">
                        <?php foreach ($followUps as $fu):
                            $icon = match ($fu->type) {
                                'call' => '<i class="fas fa-phone"></i>',
                                'meeting' => '<i class="fas fa-handshake"></i>',
                                'email' => '<i class="fas fa-envelope"></i>',
                                default => '<i class="fas fa-comment"></i>'
                            };
                            $color = match ($fu->type) {
                                'call' => 'var(--info)',
                                'meeting' => 'var(--purple)',
                                'email' => 'var(--accent)',
                                default => 'var(--primary)'
                            };
                        ?>
                            <div style="position:relative; margin-bottom:24px;">
                                <div style="position:absolute; right:-38px; top:0; width:34px; height:34px; border-radius:50%; background:<?php echo $color; ?>; color:#fff; display:flex; align-items:center; justify-content:center; border:4px solid var(--card-bg); font-size:13px; z-index:2;">
                                    <?php echo $icon; ?>
                                </div>
                                <div style="background:#f8fafc; padding:16px; border-radius:8px; border:1px solid var(--border);">
                                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                        <strong style="font-size:14px; color:var(--text-dark);"><?php echo htmlspecialchars($fu->created_by_name ?? 'موظف مبيعات'); ?></strong>
                                        <span style="font-size:11px; color:var(--text-muted); font-family:monospace;"><?php echo date('Y-m-d', strtotime($fu->date)); ?></span>
                                    </div>
                                    <div style="font-size:13px; color:var(--text-body); line-height:1.6; margin-bottom:10px;">
                                        <?php echo nl2br(htmlspecialchars($fu->notes)); ?>
                                    </div>
                                    <?php if ($fu->next_action_date): ?>
                                        <div style="font-size:12px; font-weight:700; color:var(--danger); background:var(--danger-light); display:inline-block; padding:4px 8px; border-radius:4px;">
                                            <i class="fas fa-stopwatch"></i> الإجراء القادم: <?php echo date('Y-m-d', strtotime($fu->next_action_date)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align:center; padding:30px; color:var(--text-muted);">
                        <i class="fas fa-comment-slash" style="font-size:32px; margin-bottom:10px; opacity:0.5;"></i>
                        <p>لا يوجد أي سجل تواصل أو متابعة مع هذا العميل حتى الآن.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<style>
    .badge-info {
        background-color: var(--info-light);
        color: var(--info-dark);
        border: 1px solid var(--info);
    }

    .badge-primary {
        background-color: var(--primary-light);
        color: var(--primary-dark);
        border: 1px solid var(--primary);
    }

    .badge-success {
        background-color: var(--success-light);
        color: #15803d;
        border: 1px solid var(--success);
    }

    .badge-danger {
        background-color: var(--danger-light);
        color: #dc2626;
        border: 1px solid var(--danger);
    }

    .badge-purple {
        background-color: var(--purple-light);
        color: var(--purple-dark);
        border: 1px solid var(--purple);
    }

    .badge-secondary {
        background-color: var(--page-bg);
        color: var(--text-muted);
        border: 1px solid var(--border);
    }
</style>