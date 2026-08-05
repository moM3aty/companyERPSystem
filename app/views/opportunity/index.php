<?php
// المسار: app/views/opportunity/index.php
$opportunities = $data['opportunities'] ?? [];
?>

<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-bullseye" style="color:var(--primary);"></i> إدارة الفرص البيعية (Opportunities)
        </h3>
        <p style="margin:4px 0 0; font-size:13px; color:var(--text-muted);">تتبع المبيعات المحتملة وتحويل العملاء المحتملين إلى مبيعات</p>
    </div>

    <div style="display:flex; gap:10px;">
        <a href="<?php echo URL_ROOT; ?>/opportunity/create" style="padding:10px 20px; background:var(--primary); color:#fff; border-radius:8px; text-decoration:none; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; transition:0.2s; box-shadow:0 4px 10px rgba(20,184,166,0.2);">
            <i class="fas fa-plus"></i> إضافة فرصة
        </a>
    </div>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">#</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">اسم الفرصة / العميل</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">المرحلة (Stage)</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">القيمة المتوقعة</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">تاريخ الإغلاق</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; text-align:center;">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($opportunities)): foreach ($opportunities as $opp):
                        $stageClass = match ($opp->stage) {
                            'qualification' => 'badge-info',
                            'proposal' => 'badge-primary',
                            'negotiation' => 'badge-warning',
                            'closed_won' => 'badge-success',
                            'closed_lost' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        $stageLabel = match ($opp->stage) {
                            'qualification' => 'تأهيل',
                            'proposal' => 'تقديم عرض',
                            'negotiation' => 'تفاوض',
                            'closed_won' => 'تم الفوز',
                            'closed_lost' => 'تمت الخسارة',
                            default => $opp->stage
                        };
                ?>
                        <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;">
                            <td style="padding:14px 20px; color:var(--text-muted); font-size:12px; font-weight:600;"><?php echo $opp->id; ?></td>
                            <td style="padding:14px 20px;">
                                <div style="font-weight:700; color:var(--text-dark); margin-bottom:2px;"><?php echo htmlspecialchars($opp->title); ?></div>
                                <div style="font-size:12px; color:var(--text-muted);"><i class="fas fa-building"></i> <?php echo htmlspecialchars($opp->customer_name); ?></div>
                            </td>
                            <td style="padding:14px 20px;">
                                <span class="badge <?php echo $stageClass; ?>" style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;"><?php echo $stageLabel; ?></span>
                            </td>
                            <td style="padding:14px 20px; font-weight:700; color:var(--success); direction:ltr; text-align:right;">
                                <?php echo number_format($opp->estimated_value, 2); ?> <span style="font-size:10px; color:var(--text-muted);">ر.س</span>
                            </td>
                            <td style="padding:14px 20px; font-size:13px; color:var(--text-body);">
                                <?php if ($opp->expected_close_date): ?>
                                    <i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($opp->expected_close_date)); ?>
                                <?php else: ?>
                                    <span style="color:var(--text-muted);">غير محدد</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:14px 20px; text-align:center;">
                                <a href="<?php echo URL_ROOT; ?>/opportunity/show/<?php echo $opp->id; ?>" style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:var(--primary-light); color:var(--primary-dark); text-decoration:none;" title="عرض الفرصة">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:60px 20px;">
                            <i class="fas fa-bullseye" style="font-size:40px; color:var(--border); margin-bottom:12px;"></i>
                            <h4 style="margin:0 0 6px; font-size:15px; color:var(--text-dark);">لا توجد فرص بيعية</h4>
                            <p style="margin:0; font-size:13px; color:var(--text-muted);">ابدأ بإضافة فرص جديدة لتتبع المبيعات المحتملة.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* CSS classes for badges */
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

    .badge-warning {
        background-color: var(--accent-light);
        color: #b45309;
        border: 1px solid var(--accent);
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

    .badge-secondary {
        background-color: var(--page-bg);
        color: var(--text-muted);
        border: 1px solid var(--border);
    }
</style>