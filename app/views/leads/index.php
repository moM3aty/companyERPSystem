<?php
// المسار: app/views/leads/index.php
$leads = $data['leads'] ?? [];
?>

<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-bullseye" style="color:var(--primary);"></i> العملاء المحتملين (Leads)
        </h3>
        <p style="margin:4px 0 0; font-size:13px; color:var(--text-muted);">إدارة وتتبع مسار المبيعات للمهتمين الجدد</p>
    </div>

    <div style="display:flex; gap:10px;">
        <a href="<?php echo URL_ROOT; ?>/lead/create" style="padding:10px 20px; background:var(--primary); color:#fff; border-radius:8px; text-decoration:none; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; transition:0.2s; box-shadow:0 4px 10px rgba(20,184,166,0.2);">
            <i class="fas fa-plus"></i> إضافة Lead
        </a>
    </div>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">العميل / الشركة</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">التواصل</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">المصدر</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">الحالة</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">المسؤول</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; text-align:center;">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($leads)): foreach ($leads as $lead):
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
                        <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;">
                            <td style="padding:14px 20px;">
                                <div style="font-weight:700; color:var(--text-dark); font-size:14px; margin-bottom:2px;"><?php echo htmlspecialchars($lead->name); ?></div>
                                <div style="font-size:11px; color:var(--text-muted);"><i class="fas fa-building"></i> <?php echo htmlspecialchars($lead->company ?? 'بدون شركة'); ?></div>
                            </td>
                            <td style="padding:14px 20px; font-size:13px; color:var(--text-body); direction:ltr; text-align:right;">
                                <div style="margin-bottom:2px;"><?php echo htmlspecialchars($lead->phone ?? '—'); ?></div>
                                <div style="font-size:11px; color:var(--text-muted);"><?php echo htmlspecialchars($lead->email ?? '—'); ?></div>
                            </td>
                            <td style="padding:14px 20px; font-size:13px; color:var(--text-muted); font-weight:600;">
                                <?php echo htmlspecialchars(ucfirst($lead->source)); ?>
                            </td>
                            <td style="padding:14px 20px;">
                                <span class="badge <?php echo $statusClass; ?>" style="padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold;"><?php echo $statusLabel; ?></span>
                            </td>
                            <td style="padding:14px 20px; font-size:12px; color:var(--text-body);">
                                <i class="fas fa-user-tie" style="color:var(--text-muted);"></i> <?php echo htmlspecialchars($lead->assigned_name ?? 'غير معين'); ?>
                            </td>
                            <td style="padding:14px 20px; text-align:center;">
                                <a href="<?php echo URL_ROOT; ?>/lead/show/<?php echo $lead->id; ?>" style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:var(--primary-light); color:var(--primary-dark); text-decoration:none;" title="المتابعات والتفاصيل">
                                    <i class="fas fa-history"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:60px 20px;">
                            <i class="fas fa-address-book" style="font-size:40px; color:var(--border); margin-bottom:12px;"></i>
                            <h4 style="margin:0 0 6px; font-size:15px; color:var(--text-dark);">لا يوجد عملاء محتملين</h4>
                            <p style="margin:0; font-size:13px; color:var(--text-muted);">قم بإضافة المهتمين لتبدأ بتسجيل مسار التواصل معهم.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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